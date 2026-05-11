<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\IssueStatus;
use App\Models\Notification;
use App\Models\ReturnIssue;
use App\Models\ReturnIssueHistory;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnIssueController extends Controller
{
    private function isManager(): bool
    {
        return auth()->check() && (int) auth()->user()->role_id === 4;
    }

    private function bookingsIndexRoute(): string
    {
        return $this->isManager()
            ? 'manager.bookings.index'
            : 'admin.bookings.index';
    }

    private function returnIssuesIndexRoute(): string
    {
        return $this->isManager()
            ? 'manager.return-issues.index'
            : 'admin.return-issues.index';
    }

    public function index(Request $request)
    {
        $issueStatusId = $request->get('issue_status_id');

        $returnIssues = ReturnIssue::with([
            'booking.user',
            'booking.car.brand',
            'booking.status',
            'photos',
            'reporter',
            'issueStatus',
        ])
            ->when($issueStatusId, function ($query) use ($issueStatusId) {
                $query->where('issue_status_id', $issueStatusId);
            })
            ->latest('reported_at')
            ->paginate(10)
            ->withQueryString();

        $bookingStatuses = Status::whereIn('name', ['Checkup', 'Damage', 'Needs Repair'])->get();
        $issueStatuses = IssueStatus::orderBy('id')->get();

        return view('admin.adminreturn_issues_index', compact(
            'returnIssues',
            'bookingStatuses',
            'issueStatuses',
            'issueStatusId'
        ));
    }

    public function create(Request $request, Booking $booking)
    {
        $booking->load(['user', 'car.brand', 'status']);

        $prefillStatus = $request->get('status');

        return view('admin.adminreturn_issues', compact('booking', 'prefillStatus'));
    }

    public function store(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'issue_type' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_charge' => 'nullable|numeric|min:0',
            'status_name' => 'nullable|string|in:Checkup,Damage,Needs Repair',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        DB::transaction(function () use ($validated, $request, $booking) {
            $pendingStatus = IssueStatus::where('name', 'pending')->firstOrFail();

            $issue = ReturnIssue::create([
                'booking_id' => $booking->id,
                'reported_by' => auth()->id(),
                'issue_type' => $validated['issue_type'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'issue_status_id' => $pendingStatus->id,
                'estimated_charge' => $validated['estimated_charge'] ?? 0,
                'final_charge' => 0,
                'reported_at' => now(),
            ]);

            if (!empty($validated['status_name'])) {
                $status = Status::where('name', $validated['status_name'])->first();

                if ($status) {
                    $booking->update([
                        'status_id' => $status->id,
                    ]);
                }
            }

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('return-issues', config('filesystems.default'));

                    $issue->photos()->create([
                        'photo_path' => $path,
                        'caption' => null,
                    ]);
                }
            }

            Notification::create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'title' => 'Return Issue Reported',
                'message' => 'A return issue was reported for your rental: ' . $issue->title,
                'type' => 'booking',
                'link' => route('user.return-issues.show', $issue->id),
            ]);

            ReturnIssueHistory::create([
                'return_issue_id' => $issue->id,
                'issue_status_id' => $pendingStatus->id,
                'changed_by' => auth()->id(),
                'event_type' => 'created',
                'title' => 'Issue Report Created',
                'message' => 'A new return issue was reported and marked as Pending.',
                'final_charge' => 0,
                'booking_status_name' => $validated['status_name'] ?? null,
            ]);
        });

        return redirect()
            ->route($this->bookingsIndexRoute())
            ->with('success', 'Return issue created successfully.');
    }

    public function updateStatus(Request $request, ReturnIssue $returnIssue)
    {
        $validated = $request->validate([
            'issue_status_id' => 'required|exists:issue_statuses,id',
            'final_charge' => 'nullable|numeric|min:0',
            'booking_status_name' => 'nullable|string|in:Checkup,Damage,Needs Repair',
        ]);

        $result = DB::transaction(function () use ($validated, $returnIssue) {
            $issueStatus = IssueStatus::findOrFail($validated['issue_status_id']);

            $oldIssueStatusId = (int) $returnIssue->issue_status_id;
            $newIssueStatusId = (int) $issueStatus->id;
            $statusChanged = $oldIssueStatusId !== $newIssueStatusId;

            $oldFinalCharge = (float) $returnIssue->final_charge;
            $newFinalCharge = array_key_exists('final_charge', $validated) && $validated['final_charge'] !== null
                ? (float) $validated['final_charge']
                : $oldFinalCharge;

            $chargeChanged = $oldFinalCharge !== $newFinalCharge;

            $returnIssue->update([
                'status' => $issueStatus->name,
                'issue_status_id' => $issueStatus->id,
                'final_charge' => $newFinalCharge,
            ]);

            $bookingStatusChanged = false;
            $updatedBookingStatusName = optional($returnIssue->booking->status)->name;

            if (!empty($validated['booking_status_name'])) {
                $bookingStatus = Status::where('name', $validated['booking_status_name'])->first();

                if ($bookingStatus && (int) $returnIssue->booking->status_id !== (int) $bookingStatus->id) {
                    $returnIssue->booking->update([
                        'status_id' => $bookingStatus->id,
                    ]);

                    $bookingStatusChanged = true;
                    $updatedBookingStatusName = $bookingStatus->name;
                }
            }

            if ($statusChanged || $chargeChanged || $bookingStatusChanged) {
                $historyTitle = 'Issue Updated';
                $historyMessageParts = [];

                if ($statusChanged) {
                    $historyTitle = 'Issue Status Updated';
                    $historyMessageParts[] = 'Status changed to ' . ($issueStatus->label ?? ucfirst($issueStatus->name)) . '.';
                }

                if ($chargeChanged) {
                    $historyMessageParts[] = 'Final charge updated to ₱' . number_format($newFinalCharge, 2) . '.';
                }

                if ($bookingStatusChanged) {
                    $historyMessageParts[] = 'Booking status changed to ' . $updatedBookingStatusName . '.';
                }

                ReturnIssueHistory::create([
                    'return_issue_id' => $returnIssue->id,
                    'issue_status_id' => $issueStatus->id,
                    'changed_by' => auth()->id(),
                    'event_type' => 'updated',
                    'title' => $historyTitle,
                    'message' => implode(' ', $historyMessageParts),
                    'final_charge' => $newFinalCharge,
                    'booking_status_name' => $updatedBookingStatusName,
                ]);
            }

            if ($statusChanged) {
                Notification::create([
                    'user_id' => $returnIssue->booking->user_id,
                    'booking_id' => $returnIssue->booking->id,
                    'title' => 'Return Issue Updated',
                    'message' => 'Your return issue "' . $returnIssue->title . '" was updated to ' . ($issueStatus->label ?? ucfirst($issueStatus->name)) . '.',
                    'type' => 'booking',
                    'link' => route('user.return-issues.show', $returnIssue->id),
                ]);
            }

            return [
                'success' => true,
                'status_changed' => $statusChanged,
                'booking_status_changed' => $bookingStatusChanged,
                'issue_id' => $returnIssue->id,
                'issue_status_id' => $issueStatus->id,
                'issue_status_name' => $issueStatus->name,
                'issue_status_label' => $issueStatus->label ?? ucfirst($issueStatus->name),
                'final_charge' => $newFinalCharge,
                'booking_status_name' => $updatedBookingStatusName,
            ];
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return redirect()
            ->route($this->returnIssuesIndexRoute())
            ->with('success', 'Return issue updated successfully.');
    }
}
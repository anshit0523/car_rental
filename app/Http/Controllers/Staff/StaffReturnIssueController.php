<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\IssueStatus;
use App\Models\Notification;
use App\Models\ReturnIssue;
use App\Models\ReturnIssueHistory;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffReturnIssueController extends Controller
{
    public function create(Request $request, Booking $booking)
    {
        $booking->load(['user', 'car.brand', 'status']);

        $prefillStatus = $request->get('status');

        return view('staff.staffreturn_issues', compact('booking', 'prefillStatus'));
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
                'status' => $pendingStatus->name, // keep for compatibility if your UI still uses this column
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
                    $path = $photo->store('return-issues', 'public');

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
            ->route('staff.bookings.index')
            ->with('success', 'Return issue created successfully.');
    }
}
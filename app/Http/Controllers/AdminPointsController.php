<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPointsController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->select('users.*')
            ->selectSub(function ($query) {
                $query->from('points_transactions')
                    ->selectRaw('COALESCE(SUM(points_change), 0)')
                    ->whereColumn('points_transactions.user_id', 'users.id')
                    ->where('points_change', '>', 0);
            }, 'total_earned')
            ->selectSub(function ($query) {
                $query->from('points_transactions')
                    ->selectRaw('ABS(COALESCE(SUM(points_change), 0))')
                    ->whereColumn('points_transactions.user_id', 'users.id')
                    ->where('points_change', '<', 0);
            }, 'total_redeemed')
            ->selectSub(function ($query) {
                $query->from('points_transactions')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('points_transactions.user_id', 'users.id');
            }, 'last_activity')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('last_activity')
            ->paginate(10)
            ->withQueryString();

        $transactions = DB::table('points_transactions')
            ->leftJoin('users', 'points_transactions.user_id', '=', 'users.id')
            ->leftJoin('points_transaction_types', 'points_transactions.points_id', '=', 'points_transaction_types.id')
            ->select(
                'points_transactions.*',
                'users.name as user_name',
                'users.email as user_email',
                'points_transaction_types.name as type_name'
            )
            ->latest('points_transactions.created_at')
            ->paginate(10, ['*'], 'transactions_page');

        return view('admin.admin-points-index', compact('users', 'transactions'));
    }

    public function adjust(Request $request, User $user)
    {
        $validated = $request->validate([
            'action' => 'required|in:add,deduct',
            'points' => 'required|integer|min:1',
            'note' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($validated, $user) {
            $userRow = DB::table('users')
                ->where('id', $user->id)
                ->lockForUpdate()
                ->first();

            $balanceBefore = (int) ($userRow->points_balance ?? 0);
            $pointsAmount = (int) $validated['points'];

            if ($validated['action'] === 'deduct') {
                $pointsAmount = min($pointsAmount, $balanceBefore);
                $pointsChange = -$pointsAmount;
                $typeName = 'manual_deduct';
                $notificationTitle = 'Reward Points Deducted';
                $notificationMessage = "{$pointsAmount} reward points were deducted from your account. Reason: {$validated['note']}";
            } else {
                $pointsChange = $pointsAmount;
                $typeName = 'manual_add';
                $notificationTitle = 'Reward Points Added';
                $notificationMessage = "{$pointsAmount} reward points were added to your account. Reason: {$validated['note']}";
            }

            $balanceAfter = $balanceBefore + $pointsChange;

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'points_balance' => $balanceAfter,
                    'updated_at' => now(),
                ]);

            $typeId = DB::table('points_transaction_types')
                ->where('name', $typeName)
                ->value('id');

            if (!$typeId) {
                $typeId = DB::table('points_transaction_types')->insertGetId([
                    'name' => $typeName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('points_transactions')->insert([
                'user_id' => $user->id,
                'booking_id' => null,
                'points_id' => $typeId,
                'points_change' => $pointsChange,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'note' => $validated['note'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Notification::create([
                'user_id' => $user->id,
                'booking_id' => null,
                'title' => $notificationTitle,
                'message' => $notificationMessage . " Current Balance: {$balanceAfter} Points.",
                'type' => 'points',
                'link' => route('user.rewards'),
            ]);
        });

        return back()->with('success', 'User points updated successfully.');
    }
}
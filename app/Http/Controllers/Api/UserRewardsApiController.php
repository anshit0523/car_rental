<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRewardsApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $filter = strtolower($request->get('filter', 'all'));

        $currentBalance = (int) ($user->points_balance ?? 0);

        $totalEarned = (int) DB::table('points_transactions')
            ->where('user_id', $user->id)
            ->where('points_change', '>', 0)
            ->sum('points_change');

        $totalRedeemed = abs((int) DB::table('points_transactions')
            ->where('user_id', $user->id)
            ->where('points_change', '<', 0)
            ->sum('points_change'));

        $transactions = DB::table('points_transactions')
            ->where('user_id', $user->id)
            ->when($filter === 'earned', fn ($q) => $q->where('points_change', '>', 0))
            ->when($filter === 'redeemed', fn ($q) => $q->where('points_change', '<', 0))
            ->latest('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'current_balance' => $currentBalance,
            'total_earned' => $totalEarned,
            'total_redeemed' => $totalRedeemed,
            'available_discount' => $currentBalance / 10,
            'transactions' => $transactions,
        ]);
    }
}
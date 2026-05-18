<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class UserRewardsController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $currentBalance = (int) (auth()->user()->points_balance ?? 0);

        $totalEarned = (int) DB::table('points_transactions')
            ->where('user_id', $userId)
            ->where('points_change', '>', 0)
            ->sum('points_change');

        $totalRedeemed = abs((int) DB::table('points_transactions')
            ->where('user_id', $userId)
            ->where('points_change', '<', 0)
            ->sum('points_change'));

        $transactions = DB::table('points_transactions')
            ->where('user_id', $userId)
            ->latest('created_at')
            ->paginate(10);

        return view('user.rewards', compact(
            'currentBalance',
            'totalEarned',
            'totalRedeemed',
            'transactions'
        ));
    }
}
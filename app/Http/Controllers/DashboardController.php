<?php

namespace App\Http\Controllers;

use App\Models\Engagement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * @group Dashboard
 * APIs for dashboard page
 */
class DashboardController extends Controller
{
    /*
     * Get User Engagement Datas
     */
    public function engageDatas()
    {
        // total users
        // today new users
        // this month engagement
        // today engagement

        $totalUsers = User::count();
        $todayUsers = User::whereDate('created_at', Carbon::today())->count();
        $thisMonthEngagement = Engagement::whereBetween('created_at', [now()->subMonth(), now()])->count();
        $todayEngagement = Engagement::whereDate('created_at', Carbon::today())->count();

        return response()->json([
            'total_users' => $totalUsers,
            'today_new_users' => $todayUsers,
            'this_month_engagement' => $thisMonthEngagement,
            'today_engagement' => $todayEngagement
        ]);
    }
}

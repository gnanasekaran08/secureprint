<?php
namespace App\Http\Controllers;

use App\Models\PrintJob;
use App\Models\Shop;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $overallShopsCount = Shop::count();
            $todayPrintsCount  = PrintJob::whereDate('created_at', now()->today()->toDateString())->count();

            return inertia('Dashboard', [
                'data' => [
                    'overall_shops_count' => $overallShopsCount,
                    'today_prints_count'  => $todayPrintsCount,
                ],
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the dashboard. Please try again later. ERROR : ' . $e->getMessage());
        }
    }
}

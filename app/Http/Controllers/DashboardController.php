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
            $overallShopsCount = Shop::when("shop" === auth()->user()->type, function ($query) {
                return $query->where('user_id', auth()->id());
            })->count();

            $todayPrintsCount = PrintJob::when("shop" === auth()->user()->type, function ($query) {
                return $query->whereHas('shop', function ($q) {
                    $q->where('user_id', auth()->id());
                });
            })->whereDate('created_at', now()->today()->toDateString())->count();

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

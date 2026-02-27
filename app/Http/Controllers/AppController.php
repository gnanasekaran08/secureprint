<?php
namespace App\Http\Controllers;

use App\Models\PrintJob;
use App\Models\Shop;
use Illuminate\Support\Number;

class AppController extends Controller
{
    public function index()
    {
        try {
            return inertia('Landing', [
                'data' => [
                    'shops_count'  => Number::forHumans(Shop::count()),
                    'prints_count' => Number::forHumans(PrintJob::count()),
                ],
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the application. Please try again later.');
        }
    }
}

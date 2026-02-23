<?php
namespace App\Http\Controllers;

use App\Models\Shop;
use Exception;
use Illuminate\Http\Request;

class ShopListController extends Controller
{
    public function index(Request $request)
    {
        try {
            $shops = Shop::query()
                ->when("shop" === auth()->user()->type, fn($query) => $query->where('user_id', auth()->id()))
                ->withCount('today_print_jobs')->orderBy('created_at', 'desc')->paginate(25);

            $formattedShops = $shops->getCollection()->transform(function ($shop) {
                return [
                    'id'                     => $shop->id,
                    'shop_uuid'              => $shop->uuid,
                    'name'                   => $shop->name,
                    'owner_name'             => $shop->owner->name,
                    'email'                  => $shop->owner->email,
                    'mobile_number'          => $shop->owner->mobile_number ?? null,
                    'created_at'             => $shop->created_at->toDateTimeString(),
                    'today_print_jobs_count' => $shop->today_print_jobs_count,
                    'qr_code_url'            => route('print', ['shop_uuid' => $shop->uuid]),
                ];
            });

            return inertia('Shops', [
                'shops'      => $formattedShops,
                'pagination' => [
                    'total'        => $shops->total(),
                    'per_page'     => $shops->perPage(),
                    'current_page' => $shops->currentPage(),
                    'last_page'    => $shops->lastPage(),
                    'from'         => $shops->firstItem(),
                    'to'           => $shops->lastItem(),
                ],
            ]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to load shops. Please try again later. ERROR: ' . $e->getMessage()]);
        }
    }
}

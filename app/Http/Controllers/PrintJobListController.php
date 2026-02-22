<?php
namespace App\Http\Controllers;

use App\Models\PrintJob;
use Exception;
use Illuminate\Http\Request;

class PrintJobListController extends Controller
{
    public function index(Request $request)
    {
        try {
            $printJobs = PrintJob::with('shop', 'attachments')->orderBy('created_at', 'desc')->paginate(25);

            return inertia('PrintJobsList', [
                'print_jobs' => $printJobs->items(),
                'pagination' => [
                    'total'        => $printJobs->total(),
                    'per_page'     => $printJobs->perPage(),
                    'current_page' => $printJobs->currentPage(),
                    'last_page'    => $printJobs->lastPage(),
                    'from'         => $printJobs->firstItem(),
                    'to'           => $printJobs->lastItem(),
                ],
            ]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to load shops. Please try again later. ERROR: ' . $e->getMessage()]);
        }
    }
}

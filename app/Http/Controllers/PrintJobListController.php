<?php
namespace App\Http\Controllers;

use App\Models\PrintJob;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrintJobListController extends Controller
{
    public function index(Request $request)
    {
        try {
            $printJobs = PrintJob::query()
                ->when("shop" === auth()->user()->type, function ($query) {
                    return $query->whereHas('shop', function ($q) {
                        $q->where('user_id', auth()->id());
                    });
                })
                ->with('shop', 'attachments')
                ->whereNull('removed_at')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return inertia('PrintJobsList', [
                'print_jobs' => $printJobs->toArray(),
            ]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to load shops. Please try again later. ERROR: ' . $e->getMessage()]);
        }
    }

    public function removeFiles($uuid)
    {
        try {
            $printJob = PrintJob::where('job_uuid', $uuid)->first();

            if (! $printJob) {
                return response()->json(['status' => 'error', 'message' => 'No files found for this print job.'], 404);
            }

            if (Storage::disk('public')->exists('print-jobs/' . $printJob->job_uuid)) {
                Storage::disk('public')->deleteDirectory('print-jobs/' . $printJob->job_uuid);
            }

            $printJob->removed_at = now();
            $printJob->save();

            return response()->json(['status' => 'success', 'message' => 'Print job files removed successfully.'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to remove print job files. Please try again later. ERROR: ' . $e->getMessage()], 500);
        }
    }
}

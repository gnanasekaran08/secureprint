<?php
namespace App\Http\Controllers;

use App\Models\PrintJob;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function removeFiles($uuid)
    {
        try {
            $printJob = PrintJob::where('job_uuid', $uuid)->first();

            if (! $printJob) {
                return response()->json(['status' => 'error', 'message' => 'No files found for this print job.'], 404);
            }

            foreach ($printJob->attachments as $attachment) {
                Log::info('Deleting file for attachment', ['attachment_id' => $attachment->id, 'filepath' => $attachment->filepath]);
                $attachment->deleteFile();
                $attachment->delete();
            }

            $printJob->delete();

            return response()->json(['status' => 'success', 'message' => 'Print job files removed successfully.'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to remove print job files. Please try again later. ERROR: ' . $e->getMessage()], 500);
        }
    }
}

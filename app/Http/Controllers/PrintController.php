<?php
namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\PrintJob;
use App\Models\Shop;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PrintController extends Controller
{
    /**
     * Show the QR code scanner page.
     */
    public function scan()
    {
        return Inertia::render('ScanQR');
    }

    /**
     * Show the print upload page.
     */
    public function print(Request $request, ?string $shop_uuid = null)
    {
        $shop = null;

        if ($shop_uuid) {
            $shop = Shop::where('uuid', $shop_uuid)->first();
        }

        return Inertia::render('PrintUpload', [
            'shop'     => $shop,
            'shopUuid' => $shop_uuid,
        ]);
    }

    /**
     * Upload files and create a print job.
     */
    public function upload(Request $request)
    {
        Log::info('Received print upload request', [
            'files' => $request->file('files') ? count($request->file('files')) : 0,
        ]);

        $request->validate([
            'files'           => 'required|array|min:1',
            'files.*'         => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'shop_uuid'       => 'nullable|string|exists:shops,uuid',
            'copies'          => 'required|integer|min:1|max:100',
            'is_color'        => 'required|boolean',
            'is_double_sided' => 'required|boolean',
        ]);

        Log::info('Creating print job', [
            'shop_uuid'       => $request->shop_uuid,
            'copies'          => $request->copies,
            'is_color'        => $request->is_color,
            'is_double_sided' => $request->is_double_sided,
        ]);

        $shop = null;
        if ($request->shop_uuid) {
            $shop = Shop::where('uuid', $request->shop_uuid)->first();
        }

        if (! $shop) {
            $shop = Shop::where('id', 1)->first();
        }

        // Create print job
        $printJob = PrintJob::create([
            'shop_id'         => $shop?->id,
            'job_uuid'        => Str::uuid(),
            'user_id'         => 1,
            'doc_no'          => rand(100000, 999999),
            'status'          => 'pending',
            'total_copies'    => $request->copies,
            'is_color'        => $request->is_color,
            'is_double_sided' => $request->is_double_sided,
            'otp'             => rand(1000, 9999),
            'otp_expires_at'  => now()->addHours(24),
        ]);

        $totalPages = 0;

        // Store files
        foreach ($request->file('files') as $file) {
            $filename = $file->getClientOriginalName();
            $filepath = $file->store('print-jobs/' . $printJob->job_uuid, 'public');

            // Estimate pages (simplified - in real app you'd parse PDF)
            $pages       = $this->estimatePages($file);
            $totalPages += $pages;

            Attachment::create([
                'print_job_id' => $printJob->id,
                'filename'     => $filename,
                'filepath'     => $filepath,
                'filesize'     => $file->getSize(),
                'filetype'     => $file->getClientMimeType(),
            ]);
        }

        // Calculate cost (in Rupees)
        $pricePerPage = $request->is_color ? 15 : 5;
        $totalCost    = $totalPages * $request->copies * $pricePerPage;

        if ($request->is_double_sided) {
            $totalCost *= 0.9; // 10% discount for double-sided
        }

        $printJob->update([
            'total_pages' => $totalPages,
            'total_cost'  => round($totalCost, 0),
        ]);

        return response()->json([
            'success'   => true,
            'print_job' => $printJob->fresh()->load('attachments'),
        ]);
    }

    /**
     * Process payment (mock implementation for GPay).
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'print_job_id'   => 'required|exists:print_jobs,id',
            'payment_method' => 'required|in:gpay,card,cash',
        ]);

        try {
            $printJob = PrintJob::findOrFail($request->print_job_id);

            // In a real app, you'd integrate with actual payment gateway
            // For now, we'll mock the payment
            $printJob->update([
                'status'       => 'paid',
                'submitted_at' => now(),
            ]);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Payment successful!',
                'otp'       => $printJob->otp,
                'print_job' => $printJob,
                'doc_no'    => $printJob->doc_no,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Payment failed. Please try again. ERROR: ' . $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Estimate number of pages in a file.
     */
    private function estimatePages($file): int
    {
        $extension = strtolower($file->getClientOriginalExtension());

        // For images, count as 1 page
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return 1;
        }

        // For PDFs, try to get page count (simplified)
        if ($extension === 'pdf') {
            // In a real app, you'd use a PDF library
            // For now, estimate based on file size
            $sizeInKb = $file->getSize() / 1024;

            return max(1, (int) ceil($sizeInKb / 100));
        }

        // For documents
        if (in_array($extension, ['doc', 'docx'])) {
            $sizeInKb = $file->getSize() / 1024;

            return max(1, (int) ceil($sizeInKb / 50));
        }

        return 1;
    }

    /**
     * Verify OTP for a print job.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'print_job_uuid' => 'required|string',
            'otp'            => 'required|string|size:4',
        ]);

        $printJob = PrintJob::where('job_uuid', $request->print_job_uuid)->first();

        if (! $printJob) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Print job not found.',
            ], 404);
        }

        if ($printJob->otp_expires_at && now()->gt($printJob->otp_expires_at)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'OTP has expired. Please create a new print job.',
            ], 400);
        }

        if ((int) $printJob->otp !== (int) $request->otp) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid OTP. Please try again.',
            ], 400);
        }

        try {

            // Mark as verified/ready to print
            $printJob->update([
                'status' => 'verified',
            ]);

            $files = $printJob->attachments()
                ->get()
                ->map(function ($attachment) {
                    return [
                        'filename' => $attachment->filename,
                        'filepath' => asset('storage/' . $attachment->filepath),
                    ];
                });

            return response()->json([
                'status'  => 'success',
                'message' => 'OTP verified successfully!',
                'data'    => [
                    'print_job_uuid' => $printJob->job_uuid,
                    'shop_id'        => $printJob->shop_id,
                    'files'          => $files,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while verifying OTP. Please try again. ERROR: ' . $e->getMessage(),
            ], 500);
        }

    }

    public function deletePrintJobByDocNo(Request $request)
    {
        $validator = $request->validate([
            'doc_no' => 'required|integer',
        ]);

        if (! $validator) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed. Please provide a valid doc_no.',
            ], 400);
        }

        $printJob = PrintJob::where('doc_no', $request->doc_no)->first();

        if (! $printJob) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Print job not found.',
            ], 404);
        }

        try {
            if (Storage::disk('public')->exists('print-jobs/' . $printJob->job_uuid)) {
                Storage::disk('public')->deleteDirectory('print-jobs/' . $printJob->job_uuid);
            }

            $printJob->removed_at = now();
            $printJob->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Print job and associated files deleted successfully.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while deleting the print job. Please try again. ERROR: ' . $e->getMessage(),
            ], 500);
        }

    }
}

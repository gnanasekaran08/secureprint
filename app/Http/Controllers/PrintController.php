<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\PrintJob;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PrintController extends Controller
{
    /**
     * Show the print upload page.
     */
    public function index(Request $request, ?string $shopUuid = null)
    {
        $shop = null;

        if ($shopUuid) {
            $shop = Shop::where('uuid', $shopUuid)->first();
        }

        return Inertia::render('PrintUpload', [
            'shop' => $shop,
            'shopUuid' => $shopUuid,
        ]);
    }

    /**
     * Upload files and create a print job.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'shop_uuid' => 'nullable|string|exists:shops,uuid',
            'copies' => 'required|integer|min:1|max:100',
            'is_color' => 'required|boolean',
            'is_double_sided' => 'required|boolean',
        ]);

        $shop = null;
        if ($request->shop_uuid) {
            $shop = Shop::where('uuid', $request->shop_uuid)->first();
        }

        // Create print job
        $printJob = PrintJob::create([
            'shop_id' => $shop?->id,
            'job_uuid' => Str::uuid(),
            'user_id' => auth()->id(),
            'status' => 'pending',
            'total_copies' => $request->copies,
            'is_color' => $request->is_color,
            'is_double_sided' => $request->is_double_sided,
            'otp' => rand(1000, 9999),
            'otp_expires_at' => now()->addHours(24),
        ]);

        $totalPages = 0;

        // Store files
        foreach ($request->file('files') as $file) {
            $filename = $file->getClientOriginalName();
            $filepath = $file->store('print-jobs/'.$printJob->job_uuid, 'local');

            // Estimate pages (simplified - in real app you'd parse PDF)
            $pages = $this->estimatePages($file);
            $totalPages += $pages;

            Attachment::create([
                'print_job_id' => $printJob->id,
                'filename' => $filename,
                'filepath' => $filepath,
                'filesize' => $file->getSize(),
                'filetype' => $file->getClientMimeType(),
            ]);
        }

        // Calculate cost
        $pricePerPage = $request->is_color ? 0.15 : 0.05;
        $totalCost = $totalPages * $request->copies * $pricePerPage;

        if ($request->is_double_sided) {
            $totalCost *= 0.9; // 10% discount for double-sided
        }

        $printJob->update([
            'total_pages' => $totalPages,
            'total_cost' => round($totalCost, 2),
        ]);

        return response()->json([
            'success' => true,
            'print_job' => $printJob->fresh()->load('attachments'),
        ]);
    }

    /**
     * Process payment (mock implementation for GPay).
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'print_job_id' => 'required|exists:print_jobs,id',
            'payment_method' => 'required|in:gpay,card,cash',
        ]);

        $printJob = PrintJob::findOrFail($request->print_job_id);

        // In a real app, you'd integrate with actual payment gateway
        // For now, we'll mock the payment
        $printJob->update([
            'status' => 'paid',
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment successful!',
            'otp' => $printJob->otp,
            'print_job' => $printJob,
        ]);
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
}

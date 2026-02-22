<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Attachment extends Model
{
    /** @use HasFactory<\Database\Factories\AttachmentFactory> */
    use HasFactory;

    protected $fillable = [
        'print_job_id',
        'filename',
        'filepath',
        'filesize',
        'filetype',
    ];

    public function deleteFile()
    {
        Log::info("Attempting to delete file for attachment ID: {$this->id}, filepath: {$this->filepath}");
        if (file_exists($this->filepath)) {
            Log::info("File exists. Deleting file: {$this->filepath}");
            unlink($this->filepath);
        }
    }
}

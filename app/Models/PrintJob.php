<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintJob extends Model
{
    /** @use HasFactory<\Database\Factories\PrintJobFactory> */
    use HasFactory;

    protected $fillable = [
        'job_uuid',
        'user_id',
        'printer',
        'error_message',
        'status',
        'submitted_at',
        'completed_at',
        'total_cost',
        'total_pages',
        'total_copies',
        'is_color',
        'is_double_sided',
        'is_portrait',
        'otp',
        'otp_expires_at',
    ];
}
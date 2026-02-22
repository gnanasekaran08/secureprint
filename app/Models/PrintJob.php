<?php
namespace App\Models;

use App\Models\Attachment;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintJob extends Model
{
    /** @use HasFactory<\Database\Factories\PrintJobFactory> */
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'job_uuid',
        'print_code',
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
        'removed_at',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}

<?php
namespace App\Models;

use App\Models\PrintJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    /** @use HasFactory<\Database\Factories\ShopFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'user_id',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function today_print_jobs()
    {
        return $this->hasMany(PrintJob::class, 'shop_id')
            ->whereDate('created_at', now()->toDateString());
    }
}

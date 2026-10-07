<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanLog extends Model
{
    protected $fillable = [
        'product_id',
        'scanned_code',
        'device_ip',
        'user_agent',
        'action_taken',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

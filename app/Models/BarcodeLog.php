<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarcodeLog extends Model
{
    protected $fillable = [
        'product_id',
        'action',
        'ip_address'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
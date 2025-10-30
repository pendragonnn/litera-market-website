<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_status',
        'payment_proof',
    ];

    protected $dates = ['deleted_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_item_id',
        'rating',
        'comment',
    ];

    protected $dates = ['deleted_at'];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}

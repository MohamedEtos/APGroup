<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tables extends Model
{
    protected $fillable = [
        'invoice',
        'code',
        'img',
        'sender',
        'receiver',
        'qty',
        'price',
        'type',
        'date',
        'items'
    ];

    protected $casts = [
        'items' => 'array',
    ];
}

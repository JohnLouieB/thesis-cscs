<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'tendered_amount',
        'change',
        'client_name',
        'processed_by',
        'total_amount'
    ];

    protected $cast = [
        'items' => 'json'
    ];
}

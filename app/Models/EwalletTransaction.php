<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EwalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'ewallet_transaction';

    protected $guarded = [];
}

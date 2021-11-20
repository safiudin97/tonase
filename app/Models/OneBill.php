<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneBill extends Model
{
    use HasFactory;

    protected $table = 'one_billing';

    protected $guarded = [];
}

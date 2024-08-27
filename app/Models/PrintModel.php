<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintModel extends Model
{
    use HasFactory;
    protected $fillable = [
        'queue_number',
        'office_id',
        'service',
    ];
}

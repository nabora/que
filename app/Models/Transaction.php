<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_number',
        'session_id',
        'office_id',
        'service',
        'status',
    ];
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

}
    
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    
    protected $fillable = ['office_id', 'service'];

    public function office_id()
    {
        return $this->belongsTo(Office::class);
    }
}

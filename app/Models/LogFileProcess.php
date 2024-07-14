<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogFileProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'status',
        'error_message',
    ];
}

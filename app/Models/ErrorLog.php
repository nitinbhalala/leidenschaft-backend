<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ErrorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'source',
        'user',
        'message',
        'stack_trace',
        'status'
    ];

    protected $appends = ['log_id'];

    public function getLogIdAttribute()
    {
        return '#ERR-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}

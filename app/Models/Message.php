<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'is_read', // add is_read to fillable array
    ];

    protected $hidden = [
        'delete_by'
    ];
}

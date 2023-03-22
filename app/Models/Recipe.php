<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'image',
        'catgory',
        'dietary',
        'time',
        'serves',
        'method_audio',
        'allergies',
        'ingredients',
        'method'
    ];

    protected $attributes = [
        'method_audio' => '',
        'allergies' => ''
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        
    ];
}

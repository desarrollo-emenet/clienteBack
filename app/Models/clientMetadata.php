<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clientMetadata extends Model
{
    use HasFactory;


    protected $table = 'client_metadata';
    protected $fillable = ['user_id', 'numero_cliente', 'metadata', 'last_updated_at'];

    protected $casts = [
        'metadata' => 'array',
        'last_updated_at' => 'datetime',
    ];
}

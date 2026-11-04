<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserClientApp; 

class ClientMetadata extends Model
{
    use HasFactory;


    protected $table = 'client_metadata';
    protected $fillable = ['user_id', 'numero_cliente', 'metadata', 'last_updated_at'];

    protected $casts = [
        'metadata' => 'array',
        'last_updated_at' => 'datetime',
    ];

    
    public function user()
    {
        return $this->belongsTo(UserClientApp::class, 'user_id', 'id');
    }
}

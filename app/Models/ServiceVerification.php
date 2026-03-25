<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceVerification extends Model
{
    use HasFactory;

    protected $table = 'service_verifications';

    protected $fillable = [
        'user_id',
        'numero_cliente',
        'codigo',
        'expires_at',
        'intentos',        
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    //relacion con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);   
    }
    public function isExpired(): bool
    {
        return now()->gt($this->expires_at);
    }

    public function incrementIntentos(): void
    {
        $this->increment('intentos');
    }

}

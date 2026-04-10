<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';
    protected $fillable = ['user_id', 'numero_cliente'];   

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function metadata()
{
    return $this->hasOne(ClientMetadata::class, 'numero_cliente', 'numero_cliente');
}
}

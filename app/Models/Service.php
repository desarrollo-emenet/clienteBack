<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserClientApp;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';
    protected $fillable = ['user_id', 'numero_cliente'];   

    public function user()
    {
        return $this->belongsTo(UserClientApp::class , 'user_id');
    }

    public function metadata()
{
    return $this->hasOne(ClientMetadata::class, 'numero_cliente', 'numero_cliente');
}
}

<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LimpiarRegistrosTemporales extends Command
{
    protected $signature = 'registros:limpiar';
    protected $description = 'Limpiar registros temporales de la base de datos';

    public function handle()
    {
       $usuarios = User::whereNull('email_verified_at')
            //->where('updated_at', '<=', now()->subDays(7))
            ->where('created_at', '<=', now()->subMinutes(1)) 
            ->get();

        foreach ($usuarios as $user) {
            DB::transaction(function () use ($user) {
                Service::where('user_id', $user->id)->delete();
                $user->delete();
            });
            Log::info("Usuario {$user->id} -{$user->email} eliminado.");
            $this->info("Usuario {$user->id} -{$user->email} eliminado.");
        }

        return self::SUCCESS;
    }
}

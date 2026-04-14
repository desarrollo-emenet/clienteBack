<?php

namespace App\Service;

use App\Models\clientMetadata;
use App\Models\User;
use App\Service\servicios\consultaApiService;
use App\Service\servicios\validarService;
use Illuminate\Support\Facades\Cache;

class metadataService
{

    protected $consultarApiService;
    protected $validarService;
    const TTL_HOURS = 1;

    public function __construct(consultaApiService $consultaApiService, validarService $validarService)
    {
        $this->consultarApiService = $consultaApiService;
        $this->validarService = $validarService;
    }

    public function getMetadataForCliente(string $numeroCliente, User $user)
    {
        //datos de cache
        $cacheKey = $this->getCacheKey($user, $numeroCliente);

        //buscar en cache
        $cachedMetadata = cache()->get($cacheKey);
        if ($cachedMetadata) {
            return $cachedMetadata;
        }

        //Buscar registro existente
        $clientMetadata = clientMetadata::where('user_id', $user->id)
            ->where('numero_cliente', $numeroCliente)
            ->first();

        //Si no existe → crear
        if (!$clientMetadata) {
            return $this->refresh($user, $numeroCliente);
        }

        //TTL
        if ($this->isExpired($clientMetadata)) {
            try {
                return $this->refresh($user, $numeroCliente);
            } catch (\Exception $e) {
                return $clientMetadata->metadata;
            }
        }

        Cache::put($cacheKey, $clientMetadata->metadata, now()->addHours(self::TTL_HOURS));
        return $clientMetadata->metadata;
    }

    private function isExpired(clientMetadata $clientMetadata)
    {
        if (!$clientMetadata->last_updated_at) return true;

        return $clientMetadata->last_updated_at
            ->addHours(self::TTL_HOURS)
            ->isPast();
    }


    public function refresh(User $user, string $numeroCliente)
    {
        $data =  $this->consultarApiService->peticionAPI($numeroCliente, 'false');

        if (!$data) {
            throw new \Exception('Error al consultar API');
        }


        $clientMetadata = clientMetadata::updateOrCreate(
            [
                'user_id' => $user->id,
                'numero_cliente' => $numeroCliente
            ],
            [
                'metadata' => $data,
                'last_updated_at' => now()
            ]
        );

        $cacheKey = $this->getCacheKey($user, $numeroCliente);
        Cache::put($cacheKey, $clientMetadata->metadata, now()->addHours(self::TTL_HOURS));
        return $clientMetadata->metadata;
    }

    private function getCacheKey(User $user, string $numeroCliente)
    {
        return "client_metadata_{$user->id}_{$numeroCliente}";
    }

    public function eliminarMetadata(User $user, string $numeroCliente)
    {
        $cacheKey = $this->getCacheKey($user, $numeroCliente);
        Cache::forget($cacheKey);
        clientMetadata::where('user_id', $user->id)->where('numero_cliente', $numeroCliente)->delete();
    }
}

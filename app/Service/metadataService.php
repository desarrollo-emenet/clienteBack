<?php

namespace App\Service;

use App\Models\clientMetadata;
use App\Models\User;
use App\Service\servicios\consultaApiService;
use App\Service\servicios\validarService;

class metadataService
{

    protected $consultarApiService;
    protected $validarService;
    const TTL_HOURS = 5;

    public function __construct(consultaApiService $consultaApiService, validarService $validarService)
    {
        $this->consultarApiService = $consultaApiService;
        $this->validarService = $validarService;

    }

    public function getMetadataForCliente(string $numeroCliente, User $user)
    {
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

        return $clientMetadata->metadata;
    }

    private function isExpired(clientMetadata $clientMetadata)
    {
        if (!$clientMetadata->last_updated_at) return true;

        return $clientMetadata->last_updated_at
            //->addHours(self::TTL_HOURS)
            ->addMinutes(10) //prueba 
            ->isPast();
    }


    public function refresh(User $user, string $numeroCliente)
    {
        //$data = $this->validarService->validarClienteAPI($numeroCliente, false);   -----------------------------------------------------------
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
        return $clientMetadata->metadata;
    }

    public function eliminarMetadata(string $numeroCliente)
    {
        clientMetadata::where('numero_cliente', $numeroCliente)->delete();
    }
}

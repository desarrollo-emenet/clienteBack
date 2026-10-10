<?php

namespace App\Service\Notificaciones;

use App\Models\UserDevicesToken;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class notifiService
{
    protected $messaging;

    public function __construct()
    {
        $this->messaging = Firebase::messaging();
    }

    /**
     * Envía una notificación push personalizada a todos los dispositivos de un usuario.
     * 
     * @param int $userId ID del usuario en SQL Server
     * @param string $title Título visible de la notificación
     * @param string $body Mensaje/Cuerpo visible de la notificación
     * @param array $extraData Datos clave-valor para Angular (ej: ['tipo' => 'pago'])
     */

    public function enviarNotificacionPush(int $userId, string $title, string $body, array $extraData = [])
    {
        //obtener toquen de la base de datos
        $tokens = UserDevicesToken::where('user_id', $userId)->pluck('fcm_token')->toArray();
        if (empty($tokens)) {
            Log::warning("No se encontraron dispositivos registrados para el usuario ID: {$userId}");
            return false;
        }

        $exitos = 0;

        $messages = [];
        foreach ($tokens as $token) {

            try {

            $message = CloudMessage::fromArray([
                    'token' => $token, // El target explícito que faltaba
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => $extraData, // Los metadatos para Angular
                ]);

                $this->messaging->send($message);
                $exitos++;
            } catch (\Kreait\Firebase\Exception\Messaging\InvalidArgument $e) {
                UserDevicesToken::where('fcm_token', $token)->delete();
                Log::info("Token inválido removido automáticamente de la base de datos." . $e->getMessage());
            } catch (\Exception $e) {
                Log::error("Error enviando a un token específico: " . $e->getMessage());
                Log::error("Error real de Firebase FCM: " . $e->getMessage());
            }
        }
        Log::info("Proceso de notificaciones terminado para usuario {$userId}. Exitosos: {$exitos} de " . count($tokens));

        return $exitos > 0;
    }
}

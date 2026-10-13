<?php

namespace App\Console\Commands;

use App\Models\clientMetadata;
use App\Models\User;
use App\Service\Notificaciones\notifiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnviarRecordatoriosPago extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificaciones:recordatorios-pago {tipo=deuda : pago o deuda}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios de pago';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle(notifiService $notifiService): int
    {
        $tipo = $this->argument('tipo');

        if (!in_array($tipo, ['pago', 'deuda'], true)) {
            $this->error('El tipo debe ser pago o deuda.');
            return Command::FAILURE;
        }

        //$this->info("Iniciando recordatorios: {$tipo}");

        $total = 0;
        $enviados = 0;

        User::query()
            ->whereHas('clientMetadata')
            ->with('clientMetadata')
            ->chunkById(100, function ($usuarios) use ( $notifiService, $tipo, &$total, &$enviados ) {
                foreach ($usuarios as $usuario) {
                    $total++;
                    $servicios = [];

                    foreach ($usuario->clientMetadata as $metadata) {                        
                        $clasificacion = strtoupper(trim((string) data_get( $metadata->metadata, 'cliente.clasificacion', '' )));

                        if ($clasificacion === 'BAJA') {
                            continue;
                        }

                        $deuda = (float) data_get($metadata->metadata, 'cliente.deuda', 0 );
                        $numero = data_get( $metadata->metadata, 'cliente.cliente', $metadata->numero_cliente );
                        $nombre = data_get( $metadata->metadata, 'cliente.nombre', 'Cliente' );

                        $servicios[] = [
                            'numero' => $numero,
                            'nombre' => $nombre,
                            'deuda' => max(0, $deuda),
                        ];
                    }

                    if (empty($servicios)) { continue; }

                    $conDeuda = array_values(array_filter( $servicios, fn($s) => $s['deuda'] > 0 ));

                    if ($tipo === 'deuda' && empty($conDeuda)) { continue; }
                    $nombre = $servicios[0]['nombre'] ?? 'Cliente';

                    if ($tipo === 'pago') {
                        $title = '¡Recuerda tu pago emenet!';
                        $body = "Hola {$nombre}, recuerda que las " .
                            "fechas de pago son del 1 al 5 de cada mes.";
                    } else {
                        $cantidad = count($conDeuda);
                        $totalDeuda = array_sum( array_column($conDeuda, 'deuda') );

                        $detalle = [];

                        foreach ($conDeuda as $servicio) {
                            $detalle[] = "{$servicio['numero']}: adeudo de $" .
                                number_format($servicio['deuda'], 2);
                        }

                        $title = '¡Tienes pagos pendientes!';

                        $body = "Hola {$nombre}, tienes {$cantidad} " .
                            "servicio(s) con adeudo por $" .
                            number_format($totalDeuda, 2) . '. ' . implode('; ', $detalle);
                    }

                    $enviado = $notifiService->enviarNotificacionPush(
                        (int) $usuario->id,
                        $title,
                        $body,
                        [
                            'tipo' => $tipo === 'pago'
                                ? 'recordatorio_pago'
                                : 'deuda',
                        ]
                    );

                    /*if ($enviado) {
                        $enviados++;

                        Log::info('Recordatorio EMENET enviado', [
                            'user_id' => $usuario->id,
                            'tipo' => $tipo,
                            'servicios_activos' => count($servicios),
                            'servicios_con_deuda' => count($conDeuda),
                        ]);
                    }*/
                }
            });

        //$this->info("Usuarios revisados: {$total}");
        //$this->info("Usuarios notificados: {$enviados}");

        return Command::SUCCESS;
    }
}

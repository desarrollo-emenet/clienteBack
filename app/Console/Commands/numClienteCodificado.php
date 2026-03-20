<?php

namespace App\Console\Commands;

use App\Service\clientService;
use App\Service\codificacionService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as ConsoleCommand;

class numClienteCodificado extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cliente:codigo
                            {accion : codificar o descodificar}
                            {valor : Numero de cliente o codigo encriptado}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Codificar o descodificar numero de cliente';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $accion = $this->argument('accion');
        $valor = $this->argument('valor');

        try {
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            
            if ($accion === 'codificar') {
                $codigo = codificacionService::codificarClienteConLetra($valor);
                $this->info("  Número original:    {$valor}");
                $this->line("  ↓");
                $this->info("  Código encriptado:  {$codigo}");
                
            } elseif ($accion === 'descodificar') {
                $numero = codificacionService::descodificarClienteConLetra($valor);
                $this->info("  Código encriptado:  {$valor}");
                $this->line("  ↓");
                $this->info("  Número original:    {$numero}");
                
            } else {
                $this->error("Acción inválida. Use: codificar o descodificar");
                return ConsoleCommand::FAILURE;
            }
            
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            return ConsoleCommand::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return ConsoleCommand::FAILURE;
        }
    }
}

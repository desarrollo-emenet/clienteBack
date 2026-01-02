<?php

namespace App\Console\Commands;

use App\Service\clientService;
use Illuminate\Console\Command;

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
                $codigo = clientService::codificarClienteConLetra($valor);
                $this->info("  Número original:    {$valor}");
                $this->line("  ↓");
                $this->info("  Código encriptado:  {$codigo}");
                
            } elseif ($accion === 'descodificar') {
                $numero = clientService::descodificarClienteConLetra($valor);
                $this->info("  Código encriptado:  {$valor}");
                $this->line("  ↓");
                $this->info("  Número original:    {$numero}");
                
            } else {
                $this->error("Acción inválida. Use: codificar o descodificar");
                return Command::FAILURE;
            }
            
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

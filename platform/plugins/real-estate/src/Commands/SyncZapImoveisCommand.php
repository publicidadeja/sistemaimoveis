<?php

namespace Srapid\RealEstate\Commands;

use Illuminate\Console\Command;
use Srapid\RealEstate\Services\ZapImoveisService;
use Illuminate\Support\Facades\Log;

class SyncZapImoveisCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'real-estate:sync-zap-imoveis {--force : Force sync even if integration is disabled}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza imóveis aprovados com a plataforma ZAP Imóveis';

    /**
     * @var ZapImoveisService
     */
    protected $zapService;

    /**
     * Create a new command instance.
     *
     * @param ZapImoveisService $zapService
     */
    public function __construct(ZapImoveisService $zapService)
    {
        parent::__construct();
        $this->zapService = $zapService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando sincronização com ZAP Imóveis...');

        if (!$this->zapService->isEnabled() && !$this->option('force')) {
            $this->error('Integração com ZAP Imóveis não está habilitada!');
            $this->info('Use a flag --force para sincronizar mesmo assim.');
            return 1;
        }

        try {
            $this->info('Sincronizando imóveis...');
            
            $result = $this->zapService->syncAllProperties();
            
            if (!$result['success']) {
                $this->error($result['message']);
                return 1;
            }
            
            $data = $result['data'];
            
            $this->info('Sincronização concluída!');
            $this->info("Total de imóveis processados: {$data['total']}");
            $this->info("Sincronizados com sucesso: {$data['success']}");
            $this->info("Falhas: {$data['failed']}");
            
            if ($data['failed'] > 0) {
                $this->line('');
                $this->line('Erros encontrados:');
                
                foreach ($data['errors'] as $error) {
                    $this->error("Imóvel #{$error['property_id']}: {$error['message']}");
                }
            }
            
            return 0;
            
        } catch (\Exception $e) {
            Log::error('Erro ao executar comando de sincronização com ZAP Imóveis: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->error('Erro ao sincronizar: ' . $e->getMessage());
            return 1;
        }
    }
}
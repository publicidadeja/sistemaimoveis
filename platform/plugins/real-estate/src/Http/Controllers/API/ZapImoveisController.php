<?php

namespace Srapid\RealEstate\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Srapid\Base\Http\Controllers\BaseController;
use Srapid\Base\Http\Responses\BaseHttpResponse;
use Srapid\RealEstate\Models\Property;
use Srapid\RealEstate\Repositories\Interfaces\PropertyInterface;
use Srapid\RealEstate\Services\ZapImoveisService;
use Exception;

class ZapImoveisController extends BaseController
{
    /**
     * @var ZapImoveisService
     */
    protected $zapService;

    /**
     * @var PropertyInterface
     */
    protected $propertyRepository;

    /**
     * ZapImoveisController constructor.
     * 
     * @param ZapImoveisService $zapService
     * @param PropertyInterface $propertyRepository
     */
    public function __construct(ZapImoveisService $zapService, PropertyInterface $propertyRepository)
    {
        $this->zapService = $zapService;
        $this->propertyRepository = $propertyRepository;
    }

    /**
     * Verifica status da integração
     * 
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function checkStatus(Request $request, BaseHttpResponse $response)
    {
        if (!$this->zapService->isEnabled()) {
            return $response
                ->setError()
                ->setCode(403)
                ->setMessage('Integração ZAP Imóveis não está habilitada.');
        }

        return $response
            ->setMessage('Integração ZAP Imóveis está habilitada.')
            ->setData(['enabled' => true]);
    }

    /**
     * Envia um imóvel específico para o ZAP Imóveis
     * 
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function sendProperty($id, Request $request, BaseHttpResponse $response)
    {
        try {
            $property = $this->propertyRepository->findOrFail($id);
            
            if ($property->moderation_status !== 'approved') {
                return $response
                    ->setError()
                    ->setCode(400)
                    ->setMessage('Imóvel precisa estar aprovado para ser enviado ao ZAP Imóveis.');
            }
            
            $result = $this->zapService->sendProperty($property);
            
            if (!$result['success']) {
                return $response
                    ->setError()
                    ->setCode(500)
                    ->setMessage($result['message']);
            }
            
            return $response
                ->setMessage('Imóvel enviado com sucesso para o ZAP Imóveis.')
                ->setData($result['data'] ?? []);
                
        } catch (Exception $e) {
            Log::error('Erro ao enviar imóvel para ZAP Imóveis: ' . $e->getMessage(), [
                'property_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $response
                ->setError()
                ->setCode(500)
                ->setMessage('Erro ao processar requisição: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza um imóvel específico no ZAP Imóveis
     * 
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function updateProperty($id, Request $request, BaseHttpResponse $response)
    {
        try {
            $property = $this->propertyRepository->findOrFail($id);
            
            $result = $this->zapService->updateProperty($property);
            
            if (!$result['success']) {
                return $response
                    ->setError()
                    ->setCode(500)
                    ->setMessage($result['message']);
            }
            
            return $response
                ->setMessage('Imóvel atualizado com sucesso no ZAP Imóveis.')
                ->setData($result['data'] ?? []);
                
        } catch (Exception $e) {
            Log::error('Erro ao atualizar imóvel no ZAP Imóveis: ' . $e->getMessage(), [
                'property_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $response
                ->setError()
                ->setCode(500)
                ->setMessage('Erro ao processar requisição: ' . $e->getMessage());
        }
    }

    /**
     * Remove um imóvel específico do ZAP Imóveis
     * 
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function removeProperty($id, Request $request, BaseHttpResponse $response)
    {
        try {
            $property = $this->propertyRepository->findOrFail($id);
            
            $result = $this->zapService->removeProperty($property);
            
            if (!$result['success']) {
                return $response
                    ->setError()
                    ->setCode(500)
                    ->setMessage($result['message']);
            }
            
            return $response
                ->setMessage('Imóvel removido com sucesso do ZAP Imóveis.');
                
        } catch (Exception $e) {
            Log::error('Erro ao remover imóvel do ZAP Imóveis: ' . $e->getMessage(), [
                'property_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $response
                ->setError()
                ->setCode(500)
                ->setMessage('Erro ao processar requisição: ' . $e->getMessage());
        }
    }

    /**
     * Realiza sincronização de todos os imóveis aprovados com o ZAP Imóveis
     * 
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function syncAll(Request $request, BaseHttpResponse $response)
    {
        try {
            $result = $this->zapService->syncAllProperties();
            
            if (!$result['success']) {
                return $response
                    ->setError()
                    ->setCode(500)
                    ->setMessage($result['message']);
            }
            
            return $response
                ->setMessage($result['message'])
                ->setData($result['data'] ?? []);
                
        } catch (Exception $e) {
            Log::error('Erro ao sincronizar imóveis com ZAP Imóveis: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return $response
                ->setError()
                ->setCode(500)
                ->setMessage('Erro ao processar requisição: ' . $e->getMessage());
        }
    }

    /**
     * Webhook para receber notificações do ZAP Imóveis
     * 
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function webhook(Request $request, BaseHttpResponse $response)
    {
        try {
            // Valida a assinatura do webhook (se necessário)
            // $signature = $request->header('X-ZAP-Signature');
            
            // Log dos dados recebidos para análise
            Log::info('Webhook ZAP Imóveis recebido', [
                'payload' => $request->all()
            ]);
            
            // Processa os dados recebidos conforme necessário
            // Implementação dependerá das especificações do webhook
            
            return $response
                ->setMessage('Webhook recebido com sucesso');
                
        } catch (Exception $e) {
            Log::error('Erro ao processar webhook do ZAP Imóveis: ' . $e->getMessage(), [
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $response
                ->setError()
                ->setCode(500)
                ->setMessage('Erro ao processar webhook: ' . $e->getMessage());
        }
    }
}
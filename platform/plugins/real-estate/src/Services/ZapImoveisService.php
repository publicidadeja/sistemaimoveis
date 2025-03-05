<?php

namespace Srapid\RealEstate\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Srapid\RealEstate\Models\Property;
use Exception;

class ZapImoveisService
{
    protected $apiUrl;
    protected $apiKey;
    protected $apiToken;
    protected $enabled;

    public function __construct()
    {
        $this->apiUrl = config('plugins.real-estate.real-estate.zap_imoveis.api_url');
        $this->apiKey = config('plugins.real-estate.real-estate.zap_imoveis.api_key');
        $this->apiToken = config('plugins.real-estate.real-estate.zap_imoveis.api_token');
        $this->enabled = config('plugins.real-estate.real-estate.zap_imoveis.enabled');
    }

    /**
     * Verifica se a integração está habilitada
     * 
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled && $this->apiKey && $this->apiToken;
    }

    /**
     * Envia um imóvel para a plataforma ZAP Imóveis
     * 
     * @param Property $property
     * @return array
     */
    public function sendProperty(Property $property)
    {
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'Integração ZAP Imóveis não está habilitada.'];
        }

        try {
            // Prepara os dados do imóvel para o formato exigido pela API do ZAP Imóveis
            $propertyData = $this->formatPropertyData($property);

            // Realiza a requisição para a API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->post("{$this->apiUrl}/properties", $propertyData);

            if ($response->successful()) {
                // Salva o ID do imóvel na plataforma ZAP para referência futura
                $zapId = $response->json('id');
                $property->zap_id = $zapId;
                $property->save();

                return [
                    'success' => true,
                    'message' => 'Imóvel enviado com sucesso para o ZAP Imóveis.',
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Erro ao enviar imóvel para o ZAP Imóveis: ' . $response->body(),
                'status' => $response->status()
            ];
        } catch (Exception $e) {
            Log::error('Erro na integração com ZAP Imóveis: ' . $e->getMessage(), [
                'property_id' => $property->id,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao processar requisição: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Atualiza um imóvel na plataforma ZAP Imóveis
     * 
     * @param Property $property
     * @return array
     */
    public function updateProperty(Property $property)
    {
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'Integração ZAP Imóveis não está habilitada.'];
        }

        if (empty($property->zap_id)) {
            return $this->sendProperty($property);
        }

        try {
            // Prepara os dados do imóvel para o formato exigido pela API do ZAP Imóveis
            $propertyData = $this->formatPropertyData($property);

            // Realiza a requisição para a API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->put("{$this->apiUrl}/properties/{$property->zap_id}", $propertyData);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Imóvel atualizado com sucesso no ZAP Imóveis.',
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Erro ao atualizar imóvel no ZAP Imóveis: ' . $response->body(),
                'status' => $response->status()
            ];
        } catch (Exception $e) {
            Log::error('Erro na integração com ZAP Imóveis: ' . $e->getMessage(), [
                'property_id' => $property->id,
                'zap_id' => $property->zap_id,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao processar requisição: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Remove um imóvel da plataforma ZAP Imóveis
     * 
     * @param Property $property
     * @return array
     */
    public function removeProperty(Property $property)
    {
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'Integração ZAP Imóveis não está habilitada.'];
        }

        if (empty($property->zap_id)) {
            return ['success' => true, 'message' => 'Imóvel não existe no ZAP Imóveis.'];
        }

        try {
            // Realiza a requisição para a API
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->delete("{$this->apiUrl}/properties/{$property->zap_id}");

            if ($response->successful()) {
                // Remove o ID ZAP do registro do imóvel
                $property->zap_id = null;
                $property->save();

                return [
                    'success' => true,
                    'message' => 'Imóvel removido com sucesso do ZAP Imóveis.'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erro ao remover imóvel do ZAP Imóveis: ' . $response->body(),
                'status' => $response->status()
            ];
        } catch (Exception $e) {
            Log::error('Erro na integração com ZAP Imóveis: ' . $e->getMessage(), [
                'property_id' => $property->id,
                'zap_id' => $property->zap_id,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao processar requisição: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Formata os dados do imóvel para o padrão aceito pela API do ZAP Imóveis
     * 
     * @param Property $property
     * @return array
     */
    protected function formatPropertyData(Property $property)
    {
        // Converte imagens para o formato esperado
        $images = [];
        foreach ($property->images as $image) {
            $images[] = [
                'url' => $image,
                'title' => $property->name
            ];
        }

        // Mapeia o tipo de transação para o formato esperado pela API
        $listingType = strtolower($property->type) === 'rent' ? 'RENTAL' : 'SALE';

        // Converte amenidades/características
        $features = [];
        if ($property->features && is_array($property->features)) {
            foreach ($property->features as $feature) {
                $features[] = $feature;
            }
        }

        // Formata o endereço
        $address = [
            'street' => $property->location,
            'neighborhood' => $property->city_name ?? '',
            'city' => $property->city_name ?? '',
            'state' => $property->state_name ?? '',
            'zipCode' => $property->zip_code ?? '',
            'country' => 'Brasil',
        ];

        // Retorna os dados formatados conforme especificação da API
        return [
            'reference' => $property->id,
            'title' => $property->name,
            'description' => strip_tags($property->content),
            'property_type' => $this->mapPropertyType($property->category_name ?? ''),
            'listing_type' => $listingType,
            'price' => (float) $property->price,
            'currency' => 'BRL',
            'bedrooms' => (int) $property->number_bedroom,
            'bathrooms' => (int) $property->number_bathroom,
            'parking_spaces' => (int) $property->number_garage,
            'total_area' => (float) $property->square,
            'useful_area' => (float) ($property->square_text ?? $property->square),
            'address' => $address,
            'features' => $features,
            'images' => $images,
            'videos' => [],
            'active' => $property->moderation_status === 'approved',
            'featured' => (bool) $property->is_featured,
        ];
    }

    /**
     * Mapeia categorias do sistema para os tipos de propriedade aceitos pelo ZAP Imóveis
     * 
     * @param string $categoryName
     * @return string
     */
    protected function mapPropertyType($categoryName)
    {
        $categoryName = strtolower($categoryName);
        
        $map = [
            'apartamento' => 'APARTMENT',
            'casa' => 'HOUSE',
            'terreno' => 'LAND',
            'comercial' => 'COMMERCIAL',
            'sala' => 'OFFICE',
            'loja' => 'STORE',
            'galpão' => 'WAREHOUSE',
            'rural' => 'FARM',
            'hotel' => 'HOTEL',
        ];
        
        foreach ($map as $key => $value) {
            if (strpos($categoryName, $key) !== false) {
                return $value;
            }
        }
        
        // Padrão caso não encontre correspondência
        return 'RESIDENTIAL';
    }

    /**
     * Sincroniza todos os imóveis aprovados com o ZAP Imóveis
     * 
     * @return array
     */
    public function syncAllProperties()
    {
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'Integração ZAP Imóveis não está habilitada.'];
        }

        $properties = Property::where('moderation_status', 'approved')->get();
        
        $results = [
            'total' => count($properties),
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($properties as $property) {
            $result = $this->updateProperty($property);
            
            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = [
                    'property_id' => $property->id,
                    'message' => $result['message']
                ];
            }
        }

        return [
            'success' => true,
            'message' => "Sincronização concluída. {$results['success']} imóveis sincronizados com sucesso, {$results['failed']} falhas.",
            'data' => $results
        ];
    }
}
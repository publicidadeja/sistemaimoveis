<?php

namespace Srapid\RealEstate\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Srapid\Base\Http\Controllers\BaseController;
use Srapid\RealEstate\Models\Property;
use Exception;
use RvMedia;

class VRSyncController extends BaseController
{
    public function generateXml()
    {
        try {
            // Busca imóveis com eager loading das relações necessárias
            $properties = Property::where('moderation_status', 'approved')
                ->where('status', 'published')
                ->with(['city.state', 'images', 'categories', 'features'])
                ->get();

            if ($properties->isEmpty()) {
                Log::warning('Nenhuma propriedade encontrada para o feed VRSync');
                return response('Nenhuma propriedade disponível', 404);
            }

            // Inicia o documento XML com encoding UTF-8
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            $xml .= '<ListingDataFeed xmlns="http://www.vivareal.com/schemas/1.0/VRSync" 
                     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                     xsi:schemaLocation="http://www.vivareal.com/schemas/1.0/VRSync 
                     http://xml.vivareal.com/vrsync.xsd">' . PHP_EOL;

            // Adiciona o cabeçalho
            $xml .= $this->generateHeader();

            // Inicia a seção de listagens
            $xml .= '<Listings>' . PHP_EOL;

            // Adiciona cada imóvel válido
            foreach ($properties as $property) {
                $listingXml = $this->generateListing($property);
                if (!empty($listingXml)) {
                    $xml .= $listingXml;
                }
            }

            // Fecha as tags
            $xml .= '</Listings>' . PHP_EOL;
            $xml .= '</ListingDataFeed>';

            // Retorna o XML com headers apropriados
            return response($xml, 200, [
                'Content-Type' => 'application/xml; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="vrsync_feed.xml"'
            ]);

        } catch (Exception $e) {
            Log::error('Erro ao gerar XML VRSync: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response('Erro ao gerar XML: ' . $e->getMessage(), 500);
        }
    }

    private function generateHeader()
    {
        $siteName = setting('site_name') ?: config('app.name');
        $adminEmail = setting('admin_email') ?: config('mail.from.address');
        $logo = setting('theme_logo') ? RvMedia::getImageUrl(setting('theme_logo')) : '';
        
        return '<Header>
            <Provider>
                <Name>' . e($siteName) . '</Name>
                <Email>' . e($adminEmail) . '</Email>
                <LogoUrl>' . e($logo) . '</LogoUrl>
                <Website>' . e(url('/')) . '</Website>
            </Provider>
            <CreatedAt>' . date('Y-m-d\TH:i:sP') . '</CreatedAt>
        </Header>' . PHP_EOL;
    }

    private function generateListing($property)
    {
        // Validar campos obrigatórios
        if (!$property->id || !$property->name || !$property->price) {
            Log::warning("Propriedade ID {$property->id} ignorada - campos obrigatórios faltando");
            return '';
        }

        $xml = '<Listing>' . PHP_EOL;
        
        // ID do imóvel
        $xml .= '<ListingID>' . e($property->id) . '</ListingID>' . PHP_EOL;
        
        // Título com CDATA
        $xml .= '<Title><![CDATA[' . $property->name . ']]></Title>' . PHP_EOL;
        
        // Tipo de transação
        $xml .= '<TransactionType>' . 
            ($property->type == 'sale' ? 'For Sale' : 'For Rent') . 
            '</TransactionType>' . PHP_EOL;

        // Detalhes do imóvel
        $xml .= $this->generateDetails($property);
        
        // Localização
        $xml .= $this->generateLocation($property);
        
        // Mídia
        $xml .= $this->generateMedia($property);
        
        // Informações de contato
        $xml .= $this->generateContactInfo();

        $xml .= '</Listing>' . PHP_EOL;
        
        return $xml;
    }

    private function generateDetails($property)
    {
        $xml = '<Details>' . PHP_EOL;
        
        // Tipo de propriedade baseado na primeira categoria
        $categoryName = $property->categories->first() ? 
            $property->categories->first()->name : 'Other';
        $xml .= '<PropertyType>' . $this->mapPropertyType($categoryName) . '</PropertyType>' . PHP_EOL;
        
        // Áreas com validação numérica
        $xml .= '<Area>' . PHP_EOL;
        if ($property->square && is_numeric($property->square)) {
            $xml .= '<LivingArea unit="square metres">' . 
                number_format($property->square, 2, '.', '') . 
                '</LivingArea>' . PHP_EOL;
        }
        if ($property->land_area && is_numeric($property->land_area)) {
            $xml .= '<LotArea unit="square metres">' . 
                number_format($property->land_area, 2, '.', '') . 
                '</LotArea>' . PHP_EOL;
        }
        $xml .= '</Area>' . PHP_EOL;
        
        // Descrição com CDATA e limpeza de tags HTML
        $xml .= '<Description><![CDATA[' . strip_tags($property->content) . ']]></Description>' . PHP_EOL;
        
        // Características com validação
        if ($property->number_bedroom && is_numeric($property->number_bedroom)) {
            $xml .= '<Bedrooms>' . (int)$property->number_bedroom . '</Bedrooms>' . PHP_EOL;
        }
        if ($property->number_bathroom && is_numeric($property->number_bathroom)) {
            $xml .= '<Bathrooms>' . (int)$property->number_bathroom . '</Bathrooms>' . PHP_EOL;
        }
        if ($property->number_garage && is_numeric($property->number_garage)) {
            $xml .= '<Garage>' . (int)$property->number_garage . '</Garage>' . PHP_EOL;
        }
        
        // Preços com formatação adequada
        if ($property->price && is_numeric($property->price)) {
            $price = number_format($property->price, 2, '.', '');
            if ($property->type == 'sale') {
                $xml .= '<ListPrice currency="BRL">' . $price . '</ListPrice>' . PHP_EOL;
            } else {
                $xml .= '<RentalPrice currency="BRL">' . $price . '</RentalPrice>' . PHP_EOL;
            }
        }

        // Features/Amenities
        if ($property->features && $property->features->count() > 0) {
            $xml .= '<Features>' . PHP_EOL;
            foreach ($property->features as $feature) {
                $xml .= '<Feature>' . e($feature->name) . '</Feature>' . PHP_EOL;
            }
            $xml .= '</Features>' . PHP_EOL;
        }

        $xml .= '</Details>' . PHP_EOL;
        
        return $xml;
    }

    private function generateLocation($property)
    {
        $xml = '<Location displayAddress="All">' . PHP_EOL;
        $xml .= '<Country>Brasil</Country>' . PHP_EOL;
        
        // Validação dos campos de localização
        if ($property->city && $property->city->state) {
            $xml .= '<State>' . e($property->city->state->name) . '</State>' . PHP_EOL;
            $xml .= '<City>' . e($property->city->name) . '</City>' . PHP_EOL;
        }
        
        if ($property->location) {
            $xml .= '<Neighborhood>' . e($property->location) . '</Neighborhood>' . PHP_EOL;
        }
        
        if ($property->address) {
            $xml .= '<Address>' . e($property->address) . '</Address>' . PHP_EOL;
        }
        
        if ($property->zip_code) {
            $xml .= '<PostalCode>' . e($property->zip_code) . '</PostalCode>' . PHP_EOL;
        }
        
        $xml .= '</Location>' . PHP_EOL;
        
        return $xml;
    }

    private function generateMedia($property)
    {
        $xml = '<Media>' . PHP_EOL;
        
        // Adiciona imagens com URLs completas
        if ($property->images && $property->images->count() > 0) {
            foreach ($property->images as $index => $image) {
                $imageUrl = RvMedia::getImageUrl($image);
                if ($imageUrl) {
                    $xml .= '<Item medium="image" caption="' . e($property->name) . '"' . 
                           ($index === 0 ? ' primary="true"' : '') . '>' . 
                           e($imageUrl) . 
                           '</Item>' . PHP_EOL;
                }
            }
        }
        
        // Adiciona vídeo se existir
        if ($property->video_url) {
            $xml .= '<Item medium="video">' . e($property->video_url) . '</Item>' . PHP_EOL;
        }
        
        $xml .= '</Media>' . PHP_EOL;
        
        return $xml;
    }

    private function generateContactInfo()
    {
        $siteName = setting('site_name') ?: config('app.name');
        $adminEmail = setting('admin_email') ?: config('mail.from.address');
        $logo = setting('theme_logo') ? RvMedia::getImageUrl(setting('theme_logo')) : '';
        
        return '<ContactInfo>
            <Name>' . e($siteName) . '</Name>
            <Email>' . e($adminEmail) . '</Email>
            <Website>' . e(url('/')) . '</Website>
            <Logo>' . e($logo) . '</Logo>
        </ContactInfo>' . PHP_EOL;
    }

    private function mapPropertyType($categoryName)
    {
        $map = [
            'apartamento' => 'Apartment',
            'casa' => 'Home',
            'terreno' => 'Land',
            'comercial' => 'Commercial',
            'rural' => 'Farm',
            'sala' => 'Office',
            'loja' => 'Store',
            'galpão' => 'Warehouse',
            'cobertura' => 'Penthouse'
        ];
        
        $categoryName = mb_strtolower(trim($categoryName));
        
        foreach ($map as $key => $value) {
            if (strpos($categoryName, $key) !== false) {
                return $value;
            }
        }
        
        return 'Other';
    }
}
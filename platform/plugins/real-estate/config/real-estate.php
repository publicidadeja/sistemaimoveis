<?php

use Srapid\RealEstate\Notifications\ConfirmEmailNotification;

return [
    'property_expired_after_x_days'        => env('PROPERTY_EXPIRED_AFTER_X_DAYS', 45),
    'display_big_money_in_million_billion' => env('DISPLAY_BIG_MONEY_IN_MILLION_BILLION', true),

    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    |
    | This is the notification class that will be sent to users when they receive
    | a confirmation code.
    |
    */
    'notification'                         => ConfirmEmailNotification::class,

    'verify_email' => env('CMS_ACCOUNT_VERIFY_EMAIL', false),

    'use_language_v2' => env('REAL_ESTATE_USE_LANGUAGE_VERSION_2', false),
    
    /*
    |--------------------------------------------------------------------------
    | Integração ZAP Imóveis
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com a API do ZAP Imóveis
    |
    */
    'zap_imoveis' => [
        'enabled' => env('ZAP_IMOVEIS_ENABLED', false),
        'api_key' => env('ZAP_IMOVEIS_API_KEY', ''),
        'api_token' => env('ZAP_IMOVEIS_API_TOKEN', ''),
        'api_url' => env('ZAP_IMOVEIS_API_URL', 'https://api.zapimoveis.com.br'),
        'sync_interval' => env('ZAP_IMOVEIS_SYNC_INTERVAL', 60), // em minutos
    ],
];

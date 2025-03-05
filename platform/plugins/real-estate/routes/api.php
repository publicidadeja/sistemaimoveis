<?php

Route::group([
    'prefix'     => 'api/v1',
    'namespace'  => 'Srapid\RealEstate\Http\Controllers\API',
    'middleware' => ['api'],
], function () {

    Route::post('register', 'AuthenticationController@register');
    Route::post('login', 'AuthenticationController@login');

    Route::post('password/forgot', 'ForgotPasswordController@sendResetLinkEmail');

    Route::post('verify-account', 'VerificationController@verify');
    Route::post('resend-verify-account-email', 'VerificationController@resend');

    Route::group(['middleware' => ['auth:account-api']], function () {
        Route::get('logout', 'AuthenticationController@logout');
        Route::get('me', 'AccountController@getProfile');
        Route::put('me', 'AccountController@updateProfile');
        Route::post('update-avatar', 'AccountController@updateAvatar');
        Route::put('change-password', 'AccountController@updatePassword');
    });

    // Rotas para integração com ZAP Imóveis
    Route::group([
        'prefix' => 'zap-imoveis',
        'middleware' => ['api'],
    ], function () {
        // Rota pública para webhook
        Route::post('webhook', 'ZapImoveisController@webhook');
        
        // Rotas protegidas por autenticação
        Route::group(['middleware' => ['auth:account-api']], function () {
            Route::get('status', 'ZapImoveisController@checkStatus');
            Route::post('sync', 'ZapImoveisController@syncAll');
            Route::post('property/{id}/send', 'ZapImoveisController@sendProperty');
            Route::put('property/{id}/update', 'ZapImoveisController@updateProperty');
            Route::delete('property/{id}/remove', 'ZapImoveisController@removeProperty');
        });
    });

});

@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    {!! Form::open(['url' => route('real-estate.settings'), 'class' => 'main-setting-form']) !!}
    <div class="max-width-1200">

        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                        <h2>{{ trans('plugins/real-estate::settings.general') }}</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">{{ trans('plugins/real-estate::settings.general_description') }}</p>
                </div>
            </div>
            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    <div class="form-group mb-3">
                        <label class="text-title-field" for="real_estate_square_unit">{{ trans('plugins/real-estate::settings.square_unit') }}</label>
                        <div class="ui-select-wrapper">
                            <select class="ui-select" name="real_estate_square_unit" id="real_estate_square_unit">
                                <option value="" @if (setting('real_estate_square_unit', 'm²') == null) selected @endif>{{ trans('plugins/real-estate::settings.square_unit_none') }}</option>
                                <option value="m²" @if (setting('real_estate_square_unit', 'm²') === 'm²') selected @endif>{{ trans('plugins/real-estate::settings.square_unit_meter') }}</option>
                                <option value="ft2" @if (setting('real_estate_square_unit', 'm²') === 'ft2') selected @endif>{{ trans('plugins/real-estate::settings.square_unit_feet') }}</option>
                                <option value="yd2" @if (setting('real_estate_square_unit', 'm²') === 'yd2') selected @endif>{{ trans('plugins/real-estate::settings.square_unit_yard') }}</option>
                            </select>
                            <svg class="svg-next-icon svg-next-icon-size-16">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                            </svg>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="real_estate_display_views_count_in_detail_page">{{ trans('plugins/real-estate::settings.display_views_count_in_detail_page') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="real_estate_display_views_count_in_detail_page"
                                   value="1"
                                   @if (setting('real_estate_display_views_count_in_detail_page', 0) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="real_estate_display_views_count_in_detail_page"
                                   value="0"
                                   @if (setting('real_estate_display_views_count_in_detail_page', 0) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                    <h2>{{ trans('plugins/real-estate::currency.currencies') }}</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">{{ trans('plugins/real-estate::currency.setting_description') }}</p>
                </div>
            </div>
            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="real_estate_convert_money_to_text_enabled">{{ trans('plugins/real-estate::settings.real_estate_convert_money_to_text_enabled') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="real_estate_convert_money_to_text_enabled"
                                   value="1"
                                   @if (setting('real_estate_convert_money_to_text_enabled', config('plugins.real-estate.real-estate.display_big_money_in_million_billion')) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="real_estate_convert_money_to_text_enabled"
                                   value="0"
                                   @if (setting('real_estate_convert_money_to_text_enabled', config('plugins.real-estate.real-estate.display_big_money_in_million_billion')) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="real_estate_enable_auto_detect_visitor_currency">{{ trans('plugins/real-estate::settings.enable_auto_detect_visitor_currency') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="real_estate_enable_auto_detect_visitor_currency"
                                   value="1"
                                   @if (setting('real_estate_enable_auto_detect_visitor_currency', 0) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="real_estate_enable_auto_detect_visitor_currency"
                                   value="0"
                                   @if (setting('real_estate_enable_auto_detect_visitor_currency', 0) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>
                    <div class="form-group mb-3 row">
                        <div class="col-sm-6">
                            <label class="text-title-field" for="real_estate_thousands_separator">{{ trans('plugins/real-estate::settings.thousands_separator') }}</label>
                            <div class="ui-select-wrapper">
                                <select class="ui-select" name="real_estate_thousands_separator" id="real_estate_thousands_separator">
                                    <option value="," @if (setting('real_estate_thousands_separator', ',') == ',') selected @endif>{{ trans('plugins/real-estate::settings.separator_comma') }}</option>
                                    <option value="." @if (setting('real_estate_thousands_separator', ',') == '.') selected @endif>{{ trans('plugins/real-estate::settings.separator_period') }}</option>
                                    <option value="space" @if (setting('real_estate_thousands_separator', ',') == 'space') selected @endif>{{ trans('plugins/real-estate::settings.separator_space') }}</option>
                                </select>
                                <svg class="svg-next-icon svg-next-icon-size-16">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                </svg>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-title-field" for="real_estate_decimal_separator">{{ trans('plugins/real-estate::settings.decimal_separator') }}</label>
                            <div class="ui-select-wrapper">
                                <select class="ui-select" name="real_estate_decimal_separator" id="real_estate_decimal_separator">
                                    <option value="." @if (setting('real_estate_decimal_separator', '.') == '.') selected @endif>{{ trans('plugins/real-estate::settings.separator_period') }}</option>
                                    <option value="," @if (setting('real_estate_decimal_separator', '.') == ',') selected @endif>{{ trans('plugins/real-estate::settings.separator_comma') }}</option>
                                    <option value="space" @if (setting('real_estate_decimal_separator', '.') == 'space') selected @endif>{{ trans('plugins/real-estate::settings.separator_space') }}</option>
                                </select>
                                <svg class="svg-next-icon svg-next-icon-size-16">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                </svg>
                            </div>
                        </div>
                    </div>

                <textarea name="currencies"
                          id="currencies"
                          class="hidden">{!! json_encode($currencies) !!}</textarea>
                    <textarea name="deleted_currencies"
                              id="deleted_currencies"
                              class="hidden"></textarea>
                    <div class="swatches-container">
                        <div class="header clearfix">
                            <div class="swatch-item">
                                {{ trans('plugins/real-estate::currency.name') }}
                            </div>
                            <div class="swatch-item">
                                {{ trans('plugins/real-estate::currency.symbol') }}
                            </div>
                            <div class="swatch-item swatch-decimals">
                                {{ trans('plugins/real-estate::currency.number_of_decimals') }}
                            </div>
                            <div class="swatch-item swatch-exchange-rate">
                                {{ trans('plugins/real-estate::currency.exchange_rate') }}
                            </div>
                            <div class="swatch-item swatch-is-prefix-symbol">
                                {{ trans('plugins/real-estate::currency.is_prefix_symbol') }}
                            </div>
                            <div class="swatch-is-default">
                                {{ trans('plugins/real-estate::currency.is_default') }}
                            </div>
                            <div class="remove-item">{{ trans('plugins/real-estate::currency.remove') }}</div>
                        </div>
                        <ul class="swatches-list">

                        </ul>
                        <a href="#" class="js-add-new-attribute">
                            {{ trans('plugins/real-estate::currency.new_currency') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                    <h2>{{ trans('plugins/real-estate::settings.title') }}</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">{{ trans('plugins/real-estate::settings.description') }}</p>
                </div>
            </div>

            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="real_estate_enabled_register">{{ trans('plugins/real-estate::settings.real_estate_enabled_register') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="real_estate_enabled_register"
                                   value="1"
                                   @if (setting('real_estate_enabled_register', 1) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label>
                            <input type="radio" name="real_estate_enabled_register"
                                   value="0"
                                   @if (setting('real_estate_enabled_register', 1) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="verify_account_email">{{ trans('plugins/real-estate::settings.verify_account_email') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="verify_account_email"
                                   value="1"
                                   @if (setting('verify_account_email', config('plugins.real-estate.real-estate.verify_email')) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label>
                            <input type="radio" name="verify_account_email"
                                   value="0"
                                   @if (setting('verify_account_email', config('plugins.real-estate.real-estate.verify_email')) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="real_estate_enable_credits_system">{{ trans('plugins/real-estate::settings.enable_credits_system') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="real_estate_enable_credits_system"
                                   value="1"
                                   @if (RealEstateHelper::isEnabledCreditsSystem()) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="real_estate_enable_credits_system"
                                   value="0"
                                   @if (!RealEstateHelper::isEnabledCreditsSystem()) checked @endif>{{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="enable_post_approval">{{ trans('plugins/real-estate::settings.enable_post_approval') }}
                        </label>
                        <label class="me-2">
                            <input type="radio" name="enable_post_approval"
                                   value="1"
                                   @if (setting('enable_post_approval', 1) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label>
                            <input type="radio" name="enable_post_approval"
                                   value="0"
                                   @if (setting('enable_post_approval', 1) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="property_expired_after_days">{{ trans('plugins/real-estate::settings.property_expired_after_days') }}
                        </label>
                        <input type="number" class="form-control" name="property_expired_after_days" value="{{ RealEstateHelper::propertyExpiredDays() }}">
                    </div>

                    @if (is_plugin_active('captcha'))
                        <div class="form-group mb-3">
                            <label class="text-title-field"
                                   for="real_estate_enable_recaptcha_in_register_page">{{ trans('plugins/real-estate::settings.enable_recaptcha_in_register_page') }}
                            </label>
                            <label class="me-2">
                                <input type="radio" name="real_estate_enable_recaptcha_in_register_page"
                                       value="1"
                                       @if (setting('real_estate_enable_recaptcha_in_register_page', 0) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                            </label>
                            <label class="me-2">
                                <input type="radio" name="real_estate_enable_recaptcha_in_register_page"
                                       value="0"
                                       @if (setting('real_estate_enable_recaptcha_in_register_page', 0) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                            </label>
                            <span class="help-ts">{{ trans('plugins/real-estate::settings.enable_recaptcha_in_register_page_description') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Integração ZAP Imóveis -->
        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                    <h2>Integração ZAP Imóveis</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">Configurações para integração com a plataforma ZAP Imóveis.</p>
                </div>
            </div>
            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="real_estate_zap_imoveis_enabled">Habilitar integração
                        </label>
                        <label class="me-2">
                            <input type="radio" name="real_estate_zap_imoveis_enabled" value="1" @if (setting('real_estate_zap_imoveis_enabled', 0) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                        </label>
                        <label>
                            <input type="radio" name="real_estate_zap_imoveis_enabled" value="0" @if (setting('real_estate_zap_imoveis_enabled', 0) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                        </label>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="real_estate_zap_imoveis_api_url">URL da API
                        </label>
                        <input type="text" class="form-control" name="real_estate_zap_imoveis_api_url" id="real_estate_zap_imoveis_api_url"
                               value="{{ setting('real_estate_zap_imoveis_api_url', 'https://api.zapimoveis.com.br') }}">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="real_estate_zap_imoveis_api_key">Chave da API (API Key)
                        </label>
                        <input type="text" class="form-control" name="real_estate_zap_imoveis_api_key" id="real_estate_zap_imoveis_api_key"
                               value="{{ setting('real_estate_zap_imoveis_api_key', '') }}">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="real_estate_zap_imoveis_api_token">Token de Acesso (Bearer Token)
                        </label>
                        <input type="text" class="form-control" name="real_estate_zap_imoveis_api_token" id="real_estate_zap_imoveis_api_token"
                               value="{{ setting('real_estate_zap_imoveis_api_token', '') }}">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="text-title-field"
                               for="real_estate_zap_imoveis_sync_interval">Intervalo de sincronização (minutos)
                        </label>
                        <input type="number" class="form-control" name="real_estate_zap_imoveis_sync_interval" id="real_estate_zap_imoveis_sync_interval"
                               value="{{ setting('real_estate_zap_imoveis_sync_interval', 60) }}">
                        <small class="form-text text-muted">Intervalo em minutos para sincronização automática de imóveis com o ZAP Imóveis.</small>
                    </div>
                    
                    @if (setting('real_estate_zap_imoveis_enabled', 0) == 1)
                        <div class="form-group mb-3">
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sync-zap-modal">
                                <i class="fas fa-sync"></i> Sincronizar imóveis agora
                            </button>
                            
                            <!-- Modal de confirmação de sincronização -->
                            <div class="modal fade" id="sync-zap-modal" tabindex="-1" role="dialog" aria-labelledby="sync-zap-modal-label" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="sync-zap-modal-label">Sincronizar com ZAP Imóveis</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Deseja iniciar a sincronização de todos os imóveis aprovados com o ZAP Imóveis?</p>
                                            <p><strong>Atenção:</strong> Este processo pode demorar dependendo da quantidade de imóveis.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="button" class="btn btn-primary" id="start-sync-zap">Sincronizar agora</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <script>
                                document.getElementById('start-sync-zap').addEventListener('click', function() {
                                    // Fecha o modal
                                    $('#sync-zap-modal').modal('hide');
                                    
                                    // Bloqueia a tela durante a sincronização
                                    Srapid.blockUI({
                                        target: document.body,
                                        message: 'Sincronizando imóveis...',
                                        overlayColor: 'rgba(0,0,0,0.1)',
                                        boxed: true
                                    });
                                    
                                    // Faz a solicitação para iniciar a sincronização
                                    $.ajax({
                                        url: '/api/v1/zap-imoveis/sync',
                                        type: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function(response) {
                                            Srapid.unblockUI(document.body);
                                            Srapid.showSuccess(response.message);
                                        },
                                        error: function(xhr) {
                                            Srapid.unblockUI(document.body);
                                            let message = 'Ocorreu um erro durante a sincronização.';
                                            
                                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                                message = xhr.responseJSON.message;
                                            }
                                            
                                            Srapid.showError(message);
                                        }
                                    });
                                });
                            </script>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        <div class="flexbox-annotated-section" style="border: none">
            <div class="flexbox-annotated-section-annotation">
                &nbsp;
            </div>
            <div class="flexbox-annotated-section-content">
                <button class="btn btn-info" type="submit">{{ trans('plugins/real-estate::currency.save_settings') }}</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@push('footer')
    <script id="currency_template" type="text/x-custom-template">
        <li data-id="__id__" class="clearfix">
            <div class="swatch-item" data-type="title">
                <input type="text" class="form-control" value="__title__">
            </div>
            <div class="swatch-item" data-type="symbol">
                <input type="text" class="form-control" value="__symbol__">
            </div>
            <div class="swatch-item swatch-decimals" data-type="decimals">
                <input type="number" class="form-control" value="__decimals__">
            </div>
            <div class="swatch-item swatch-exchange-rate" data-type="exchange_rate">
                <input type="number" class="form-control" value="__exchangeRate__" step="0.00000001">
            </div>
            <div class="swatch-item swatch-is-prefix-symbol" data-type="is_prefix_symbol">
                <div class="ui-select-wrapper">
                    <select class="ui-select">
                        <option value="1" __isPrefixSymbolChecked__>{{ trans('plugins/real-estate::currency.before_number') }}</option>
                        <option value="0" __notIsPrefixSymbolChecked__>{{ trans('plugins/real-estate::currency.after_number') }}</option>
                    </select>
                    <svg class="svg-next-icon svg-next-icon-size-16">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                    </svg>
                </div>
            </div>
            <div class="swatch-is-default" data-type="is_default">
                <input type="radio" name="currencies_is_default" value="__position__" __isDefaultChecked__>
            </div>
            <div class="remove-item"><a href="#" class="font-red"><i class="fa fa-trash"></i></a></div>
        </li>
    </script>
@endpush

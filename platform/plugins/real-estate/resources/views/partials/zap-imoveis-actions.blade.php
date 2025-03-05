@if (setting('real_estate_zap_imoveis_enabled', 0))
    <div class="btn-list">
        <h5 class="mb-2">Integração ZAP Imóveis</h5>
        @if ($property->zap_id)
            <div class="alert alert-success">
                <strong>ID ZAP: {{ $property->zap_id }}</strong>
            </div>
            <a href="javascript:void(0)" 
               class="btn btn-warning btn-sm" 
               data-toggle="tooltip" 
               data-original-title="Atualizar no ZAP Imóveis"
               onclick="syncWithZapImoveis('update', '{{ $property->id }}')">
                <i class="fas fa-sync"></i> Atualizar no ZAP
            </a>
            <a href="javascript:void(0)" 
               class="btn btn-danger btn-sm" 
               data-toggle="tooltip" 
               data-original-title="Remover do ZAP Imóveis"
               onclick="syncWithZapImoveis('remove', '{{ $property->id }}')">
                <i class="fas fa-trash"></i> Remover do ZAP
            </a>
        @else
            <div class="alert alert-secondary">
                <strong>Imóvel não sincronizado com ZAP Imóveis</strong>
            </div>
            <a href="javascript:void(0)" 
               class="btn btn-primary btn-sm" 
               data-toggle="tooltip" 
               data-original-title="Enviar para ZAP Imóveis"
               onclick="syncWithZapImoveis('send', '{{ $property->id }}')">
                <i class="fas fa-upload"></i> Enviar para ZAP
            </a>
        @endif
    </div>

    <script>
        function syncWithZapImoveis(action, propertyId) {
            let method = 'POST';
            let endpoint = '/api/v1/zap-imoveis/property/' + propertyId + '/send';
            let confirmMessage = 'Deseja enviar este imóvel para o ZAP Imóveis?';
            
            if (action === 'update') {
                method = 'PUT';
                endpoint = '/api/v1/zap-imoveis/property/' + propertyId + '/update';
                confirmMessage = 'Deseja atualizar este imóvel no ZAP Imóveis?';
            } else if (action === 'remove') {
                method = 'DELETE';
                endpoint = '/api/v1/zap-imoveis/property/' + propertyId + '/remove';
                confirmMessage = 'Deseja remover este imóvel do ZAP Imóveis?';
            }
            
            if (!confirm(confirmMessage)) {
                return;
            }
            
            // Exibe overlay de carregamento
            Srapid.blockUI({
                target: document.body,
                message: 'Processando...',
                overlayColor: 'rgba(0,0,0,0.1)',
                boxed: true
            });
            
            $.ajax({
                url: endpoint,
                type: method,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Srapid.unblockUI(document.body);
                    Srapid.showSuccess(response.message);
                    
                    // Recarrega a página após um breve atraso
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    Srapid.unblockUI(document.body);
                    let message = 'Ocorreu um erro ao processar a solicitação.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    Srapid.showError(message);
                }
            });
        }
    </script>
@endif
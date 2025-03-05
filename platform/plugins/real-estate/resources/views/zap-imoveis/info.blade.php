@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="max-width-1200">
        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                    <h2>Integração ZAP Imóveis</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">Informações para integração com o portal ZAP Imóveis.</p>
                </div>
            </div>
            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    <div class="alert alert-info">
                        <h4>Feed XML para ZAP Imóveis</h4>
                        <p>Utilize a URL abaixo para integrar seu portal com o ZAP Imóveis:</p>
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $feedUrl }}" id="zap_feed_url" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-info" type="button" onclick="copyFeedUrl()">
                                        <i class="fa fa-copy"></i> Copiar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p><a href="{{ $feedUrl }}" target="_blank" class="btn btn-primary">
                            <i class="fa fa-external-link"></i> Visualizar Feed
                        </a></p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h4>Instruções</h4>
                        <p>Para integrar seu portal com o ZAP Imóveis, siga os passos abaixo:</p>
                        <ol>
                            <li>Copie a URL do feed XML acima</li>
                            <li>Acesse sua conta no ZAP Imóveis</li>
                            <li>Na seção de integrações, adicione a URL do feed</li>
                            <li>Configure a frequência de atualização conforme necessário</li>
                        </ol>
                        <p><strong>Observação:</strong> Apenas imóveis aprovados e com status "À Venda" serão incluídos no feed.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flexbox-annotated-section" style="border: none">
            <div class="flexbox-annotated-section-annotation">
                &nbsp;
            </div>
            <div class="flexbox-annotated-section-content">
                <a href="{{ route('real-estate.settings') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Voltar para configurações
                </a>
            </div>
        </div>
    </div>
@endsection

@push('footer')
<script>
    function copyFeedUrl() {
        var copyText = document.getElementById("zap_feed_url");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        
        Srapid.showSuccess('URL do feed copiada para a área de transferência!');
    }
</script>
@endpush
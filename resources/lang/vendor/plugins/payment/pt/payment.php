<?php

return [
    'payments' => 'Pagamentos',
    'checkout_success' => 'Finalizado com sucesso!',
    'view_payment' => 'Ver pagamento #',
    'charge_id' => 'Código de cobrança',
    'amount' => 'Quantia',
    'currency' => 'Moeda',
    'user' => 'Usuário',
    'stripe' => 'Stripe',
    'paypal' => 'PayPal',
    'action' => 'Ação',
    'card_number' => 'Número do cartão',
    'full_name' => 'Nome completo',
    'payment_via_paypal' => 'Pagamento online rápido e seguro via PayPal.',
    'mm_yy' => 'MM/YY',
    'cvc' => 'CVC',
    'details' => 'Detalhes',
    'payer_name' => 'Nome do pagador',
    'email' => 'Email',
    'phone' => 'Telefone',
    'country' => 'País',
    'shipping_address' => 'endereço de entrega',
    'payment_details' => 'Detalhes do pagamento',
    'card' => 'Cartão',
    'address' => 'Endereço',
    'could_not_get_stripe_token' => 'Não foi possível obter o token Stripe para fazer uma cobrança.',
    'payment_id' => 'Código de pagamento',
    'payment_methods' => 'Métodos de Pagamento',
    'transactions' => 'Transações',
    'payment_methods_description' => 'Configurar métodos de pagamento para o site',
    'paypal_description' => 'O cliente pode comprar o produto e pagar diretamente via PayPal',
    'use' => 'Usar',
    'stripe_description' => 'O cliente pode comprar o produto e pagar diretamente com Visa, cartão de crédito via Stripe',
    'edit' => 'Editar',
    'settings' => 'Definições',
    'activate' => 'Ativar',
    'deactivate' => 'Desativar',
    'update' => 'Atualizar',
    'configuration_instruction' => 'Instrução de configuração para :name',
    'configuration_requirement' => 'Para usar :name, você precisa',
    'service_registration' => 'Registrar com :name',
    'after_service_registration_msg' => 'Após o registro em :name, você terá Client ID, Client Secret',
    'enter_client_id_and_secret' => 'Digite ID do cliente, Segredo na caixa à direita',
    'method_name' => 'Nome do método',
    'please_provide_information' => 'Por favor, forneça informações',
    'client_id' => 'ID do Cliente',
    'client_secret' => 'Segredo do cliente',
    'secret' => 'Secret',
    'stripe_key' => 'Liste a chave pública',
    'stripe_secret' => 'Chave privada de distribuição',
    'stripe_after_service_registration_msg' => 'Após o registro em :name, você terá chaves públicas e secretas',
    'stripe_enter_client_id_and_secret' => 'Digite as chaves públicas e secretas na caixa à direita',
    'pay_online_via' => 'Pague online através de :name',
    'sandbox_mode' => 'Sandbox mode',
    'deactivate_payment_method' => 'Desativar forma de pagamento',
    'deactivate_payment_method_description' => 'Deseja mesmo desativar este método de pagamento?',
    'agree' => 'Aceitar',
    'name' => 'Pagamentos',
    'create' => 'Novo pagamento',
    'go_back' => 'Volte',
    'information' => 'Em formação',
    'methods' => [
        'paypal' => 'PayPal',
        'stripe' => 'Stripe',
        'cod' => 'Dinheiro na entrega (COD)',
        'bank_transfer' => 'transferência bancária',
    ],
    'statuses' => [
        'pending' => 'Pendente',
        'completed' => 'Concluído',
        'refunding' => 'Reembolso',
        'refunded' => 'Recusado',
        'fraud' => 'Fraude',
        'failed' => 'Falhou',
    ],
    'payment_methods_instruction' => 'Oriente os clientes a pagar diretamente. Você pode optar por pagar por entrega ou transferência bancária',
    'payment_method_description' => 'Guia de pagamento - (Exibido na página de aviso de compra e pagamento bem-sucedida)',
    'payment_via_cod' => 'Dinheiro na entrega',
    'payment_via_bank_transfer' => 'transferência bancária',
    'payment_pending' => 'Finalizado com sucesso. Seu pagamento está pendente e será verificado por nossa equipe.',
    'created_at' => 'Criado em',
    'payment_channel' => 'Canal de pagamento',
    'total' => 'Total',
    'status' => 'Status',
    'default_payment_method' => 'Metodo de pagamento padrão',
    'turn_off_success' => 'Desligue o método de pagamento com sucesso!',
    'saved_payment_method_success' => 'Forma de pagamento salva com sucesso!',
    'saved_payment_settings_success' => 'Configurações de pagamento salvas com sucesso!',
    'payment_name' => 'Nome',
    'callback_url' => 'URL de retorno',
    'return_url' => 'URL de retorno',
    'payment_not_found' => 'Pagamento não encontrado!',
    'refunds' => [
        'title' => 'Reembolsos',
        'id' => 'ID',
        'breakdowns' => 'Separação',
        'gross_amount' => 'Valor bruto',
        'paypal_fee' => 'Taxa do PayPal',
        'net_amount' => 'Valor líquido',
        'total_refunded_amount' => 'Valor total reembolsado',
        'create_time' => 'Criar tempo',
        'update_time' => 'Tempo de atualização',
        'status' => 'Status',
        'description' => 'Descrição',
        'refunded_at' => 'Reembolsado em',
        'error_message' => 'Mensagem de erro',
    ],
    'view_response_source' => 'Ver fonte de resposta',
    'status_is_not_completed' => 'O status não está CONCLUÍDO',
    'cannot_found_capture_id' => 'Não foi possível encontrar o ID de captura com detalhes de pagamento',
    'amount_refunded' => 'Valor reembolsado',
    'amount_remaining' => 'Montante restante',
    'paid_at' => 'Pago em',
    'invalid_settings' => 'As configurações para :name são inválidas!',
    'view_transaction' => 'Transação ":charge_id"',
    'payment_description' => 'Pague pelo seu pedido #:order_id em :site_url',
];

<?php
require '../../config/conexao.php';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=relatorio_pedidos.csv');

$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, [
    'Nome',
    'Estado',
    'Preco',
    'Forma de Pagamento',
    'Data do Pedido'
], ';');

$stmt = $pdo->query("
    SELECT 
        tabela_pedidos.valor_total,
        tabela_pedidos.data_pedido,
        clientes.nome,
        endereco.estado,
        pagamento.metodo_pagamento
    FROM tabela_pedidos
    INNER JOIN clientes 
        ON tabela_pedidos.cliente_id = clientes.id
    INNER JOIN endereco 
        ON tabela_pedidos.endereco_entrega_id = endereco.id
    LEFT JOIN pagamento 
        ON pagamento.pedido_id = tabela_pedidos.id
");

$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

function formatarMetodoPagamento(?string $metodo): string
{
    $metodos = [
        'pix' => 'Pix',
        'credito' => 'Cartao de credito',
        'credit' => 'Cartao de credito',
        'debito' => 'Cartao de debito',
        'debit' => 'Cartao de debito',
        'boleto' => 'Boleto',
    ];

    return $metodos[$metodo ?? ''] ?? 'Nao informado';
}

foreach ($pedidos as $pedido) {
    fputcsv($output, [
        $pedido['nome'],
        $pedido['estado'],
        number_format((float) $pedido['valor_total'], 2, ',', '.'),
        formatarMetodoPagamento($pedido['metodo_pagamento'] ?? null),
        date('d/m/Y', strtotime($pedido['data_pedido']))
    ], ';');
}

fclose($output);
exit;
?>

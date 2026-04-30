<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../../config/conexao.php';

// Cabeçalhos para download
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=relatorio_pedidos.csv');

// Criar arquivo em memória
$output = fopen('php://output', 'w');

// 🔥 ESSA LINHA RESOLVE O PROBLEMA DOS ACENTOS
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Cabeçalho das colunas
fputcsv($output, [
    'Nome',
    'Estado',
    'Preço',
    'Forma de Pagamento',
    'Data do Pedido'
], ';');

// Buscar dados
$stmt = $pdo->query("
    SELECT 
        tabela_pedidos.valor_total,
        tabela_pedidos.data_pedido,
        clientes.nome,
        endereco.estado,
        pagamento.forma_pagamento
    FROM tabela_pedidos
    INNER JOIN clientes 
        ON tabela_pedidos.cliente_id = clientes.id
    INNER JOIN endereco 
        ON tabela_pedidos.endereco_entrega_id = endereco.id
    INNER JOIN pagamento 
        ON pagamento.pedido_id = tabela_pedidos.id
");

$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inserir dados
foreach ($pedidos as $pedido) {

    fputcsv($output, [
        $pedido['nome'],
        $pedido['estado'],
        $pedido['forma_pagamento'],
        number_format($pedido['valor_total'], 2, ',', '.'),
        date('d/m/Y', strtotime($pedido['data_pedido']))
    ], ';');
}

fclose($output);
exit;
?>
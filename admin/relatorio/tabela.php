<?php
require '../../config/conexao.php';

$stmt = $pdo->query("
    SELECT 
        tabela_pedidos.id,
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
$tabela_pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

function formatarMetodoPagamento(?string $metodo): string
{
    $metodos = [
        'pix' => 'Pix',
        'credito' => 'Cartão de crédito',
        'credit' => 'Cartão de crédito',
        'debito' => 'Cartão de débito',
        'debit' => 'Cartão de débito',
        'boleto' => 'Boleto',
    ];

    return $metodos[$metodo ?? ''] ?? 'Nao informado';
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Relatorio</h1>
    <a href="exportar.php" class="btn btn-success">
        Exportar
    </a>
</div>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Estado</th>
            <th>Preco</th>
            <th>Forma de pagamento</th>
            <th>Data do pedido</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tabela_pedidos as $pedido): ?>
            <tr>
                <td><?php echo htmlspecialchars($pedido['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($pedido['estado'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>R$ <?php echo number_format((float) $pedido['valor_total'], 2, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars(formatarMetodoPagamento($pedido['metodo_pagamento'] ?? null), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo date('d/m/Y', strtotime($pedido['data_pedido'])); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

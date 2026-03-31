<?php
    require '../../config/conexao.php';
    $stmt = $pdo->query("
    SELECT 
        tabela_pedidos.id,
        tabela_pedidos.valor_total,
        tabela_pedidos.data_pedido,
        clientes.nome,
        endereco.estado
    FROM tabela_pedidos
    INNER JOIN clientes 
        ON tabela_pedidos.cliente_id = clientes.id
    INNER JOIN endereco 
        ON tabela_pedidos.endereco_entrega_id = endereco.id
");
    $tabela_pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Relatório</h1>
</div>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Estado</th>
            <th>Preço</th>
            <th>Data do pedido</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tabela_pedidos as $pedido): ?>
            <tr>
                <td><?php echo $pedido['nome']; ?></td>
                <td><?php echo $pedido['estado']; ?></td>
                <td>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></td>
                <td><?php echo date('d/m/Y', strtotime($pedido['data_pedido'])); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
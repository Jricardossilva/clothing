<?php
    require '../../config/conexao.php';
    $stmt = $pdo->query("SELECT * FROM categorias");
    $categorias = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Categorias</h1>
    <a href="form.php" class="btn btn-primary">Cadastrar</a>
</div>
<table class="table table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Gênero</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categorias as $categoria): ?>
            <tr>
                <td><?php echo $categoria['id']; ?></td>
                <td><?php echo $categoria['nome']; ?></td>
                <td><?php echo $categoria['descricao']; ?></td>
                <td><?php echo $categoria['genero']; ?></td>
                <td>
                    <a href="form.php?id=<?php echo $categoria['id']; ?>" class="btn btn-warning">Editar</a>
                    <a href="deletar.php?id=<?php echo $categoria['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja deletar esta categoria?');">Deletar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

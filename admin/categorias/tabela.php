<?php
    require '../../config/conexao.php';
    $stmt = $pdo->query("SELECT * FROM categorias");
    $categorias = $stmt->fetchAll();
?>

<h1 class="mb-4">Categorias</h1>
<table class="table table-bordered">
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
                    <a href="form.php?id=<?php echo $categoria['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="deletar.php?id=<?php echo $categoria['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja deletar esta categoria?');">Deletar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<a href="form.php" class="btn btn-primary mb-3">Adicionar Categoria</a>
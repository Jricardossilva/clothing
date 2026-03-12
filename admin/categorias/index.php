<!-- listar categorias -->
<?php
    require '../../config/conexao.php';
    $stmt = $pdo->query("SELECT * FROM categorias");
    $categorias = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar categorias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
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
                            <a href="editar.php?id=<?php echo $categoria['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="deletar.php?id=<?php echo $categoria['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja deletar esta categoria?');">Deletar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="cadastrar.php" class="btn btn-primary mb-3">Adicionar Categoria</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>  
</body>
</html>
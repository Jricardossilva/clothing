<!-- cadastro de produtos -->
<?php
    require '../../config/conexao.php';

    $stmt = $pdo->query("SELECT id, nome FROM categorias");
    $categorias = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>


<body>
    <div class="container mt-5">
        <h1 class="mb-4">Adicionar Produto</h1>
        <form action="salvar.php" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="mb-3 col-md-4">
                    <label class="form-label">Nome do produto</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="mb-3 col-md-4">
                    <label class="form-label">Preço</label>
                    <input type="number" name="preco" step="0.01" class="form-control" required>
                </div>
                <div class="mb-3 col-md-4">
                    <label class="form-label">Estoque</label>
                    <input type="number" name="estoque" class="form-control" required>
                </div>
                <div class="mb-3 col-md-6">
                    <label for="formFileSm" class="form-label">Adicione uma imagem</label>
                    <input name="imagem" class="form-control form-control-sm" id="formFileSm" type="file">
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">Categoria</label>
                    <select name="categoria" id="categoria" class="form-control" required>
                        <option value="" disabled selected>Selecione uma categoria</option>

                        <?php foreach($categorias as $categoria): ?>
                            <option value="<?= $categoria['id'] ?>">
                                <?= $categoria['nome'] ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="9" required></textarea>
            </div>        
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</html>


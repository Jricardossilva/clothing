<!-- editar produto -->
<?php
    require '#';

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    $id = (int) $_GET['id'];
    $errors = [];

    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch();

    if (!$produto) {
        header("Location: index.php");
        exit;
    }   

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo 'entrei';
        $nome = trim($_POST['nome']);
        $descricao = trim($_POST['descricao']);
        $preco = filter_var($_POST['preco'], FILTER_VALIDATE_FLOAT);
        $estoque = filter_var($_POST['estoque'], FILTER_VALIDATE_INT);
        $categoria = filter_var($_POST['categoria'], FILTER_SANITIZE_STRING);
        
        if (!$nome) $errors[] = "Nome é obrigatório.";
        if ($preco === false) $errors[] = "Preço inválido.";    
        if ($estoque === false) $errors[] = "Estoque inválido.";
        if (!$descricao) $errors[] = "Descrição é obrigatório.";
        if (!$categoria) $errors[] = "Categoria é obrigatório.";

        if (!$errors){
            $sql = "UPDATE produtos SET nome = ?, descricao = ?, preco = ?, estoque = ?, categoria = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt-> execute([$nome, $descricao, $preco, $estoque, $categoria, $id]);
            
            header("Location: index.php");
            exit;
        }   else {
            $nome = $produto['nome'];
            $descricao = $produto['descricao'];
            $preco = $produto['preco'];
            $estoque = $produto['estoque'];
            $categoria = $produto['categoria'];
        }
    }


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
   <div class="container">
    <H1>Editar Produto</H1>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate id="editarProduto">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" required  
            value="<?=  htmlspecialchars($produto['nome'])?> " >     
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control"><?=  htmlspecialchars($produto['descricao'])?></textarea>  
                
        </div>
        <div class="mb-3">
            <label for="preco" class="form-label">Preço</label>
            <input type="text" name="preco" id="preco" class="form-control" required 
            value="<?=  htmlspecialchars($produto['preco'])?> " >  
        </div>
        <div class="mb-3">
            <label for="estoque" class="form-label">Estoque</label>
            <input type="text" name="estoque" id="estoque" class="form-control" required  
            value="<?= htmlspecialchars($produto['estoque'])?> " >
        </div>
        <div class="mb-3">
            <label for="categoria" class="form-label">Categoria</label>
            <input type="text" name="categoria" id="categoria" class="form-control" required  
            value="<?= htmlspecialchars($produto['categoria'])?> " >
        </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
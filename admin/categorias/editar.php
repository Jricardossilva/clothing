<!-- editar categoria -->
<?php
    require '../../config/conexao.php';

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    $id = (int) $_GET['id'];
    $errors = [];

    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([$id]);
    $categoria = $stmt->fetch();

    if (!$categoria) {
        header("Location: index.php");
        exit;
    }   
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo 'entrei';
        $nome = trim($_POST['nome']);
        $genero = trim($_POST['genero']);
        $descricao = trim($_POST['descricao']);
        
        if (!$nome) $errors[] = "Nome é obrigatório.";
        if (!$genero) $errors[] = "Genero é obrigatório.";
        if (!$descricao) $errors[] = "Descrição é obrigatório.";

        if (!$errors){
            $sql = "UPDATE categorias SET nome = ?, genero = ?, descricao = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt-> execute([$nome, $genero, $descricao, $id]);
            
            header("Location: index.php");
            exit;
        }   else {
            $nome = $produto['nome'];
            $genero = $produto['genero'];
            $descricao = $produto['descricao'];
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1>Editar Categoria</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $categoria['nome']; ?>">
            </div>
            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <input type="text" class="form-control" id="descricao" name="descricao" value="<?php echo $categoria['descricao']; ?>">
            </div>
            <div class="form-group">
                <label for="genero">Gênero:</label>
                <select class="form-select" aria-label="Default select example" name="genero" id="genero" required>
                    <option selected disabled>Selecione o gênero</option>
                    <option value="Masculino" <?php echo $categoria['genero'] === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                    <option value="Feminino" <?php echo $categoria['genero'] === 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                    <option value="Unissex" <?php echo $categoria['genero'] === 'Unissex' ? 'selected' : ''; ?>>Unissex</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Salvar</button>
            <a href="index.php" class="btn btn-secondary mt-3">Cancelar</a>
        </form>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
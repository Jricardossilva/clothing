<!-- salvar categorias -->
<?php
    require '../../config/conexao.php';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $nome = trim($_POST['nome']);
        $descricao = trim($_POST['descricao']);
        $genero = trim($_POST['genero']);

        $sql = "INSERT INTO categorias (nome, descricao, genero) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nome,
            $descricao,
            $genero
        ]);
    } else {
        echo "❌ Erro no cadastro.";
    }
    header('Location: index.php');
    exit;
?>

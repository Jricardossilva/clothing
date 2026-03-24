<!-- salvar categorias -->
<?php
    require '../../config/conexao.php';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $id = $_POST['id'] ?? null;
        $nome = trim($_POST['nome']);
        $descricao = trim($_POST['descricao']);
        $genero = trim($_POST['genero']);

        if ($id) {
            // Edição
            $sql = "UPDATE categorias SET nome=?, descricao=?, genero=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $descricao, $genero, $id]);
        } else {
            // Cadastro
            $sql = "INSERT INTO categorias (nome, descricao, genero) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $descricao, $genero]);
        }

    } else {
        echo "❌ Erro no cadastro.";
    }
    header('Location: index.php');
    exit;
?>
<!-- salvar produtos -->
<?php
require '../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = trim($_POST['preco']);
    $estoque = trim($_POST['estoque']);

    $sql = "INSERT INTO produtos (nome, descricao, preco, estoque) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $nome,
        $descricao,
        $preco,
        $estoque
    ]);
} else {
    echo "❌ Erro no cadastro.";
}
header('Location: index.php');
exit;
?>

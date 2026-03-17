<!-- salvar produtos -->
<?php
require '../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = trim($_POST['preco']);
    $estoque = trim($_POST['estoque']);
    $imagem = null;

    //salvar imagem em pasta img
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $nomeOriginal = $_FILES['imagem']['name'];
        $imagem = time() . '-' . $nomeOriginal;
        $destino = $_SERVER['DOCUMENT_ROOT'] . '/clothing/assets/img/' . $imagem;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
    }

    $sql = "INSERT INTO produtos (nome, descricao, preco, estoque, url_imagem) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $nome,
        $descricao,
        $preco,
        $estoque,
        $imagem
    ]);
} else {
    echo "❌ Erro no cadastro.";
}
header('Location: index.php');
exit;
?>

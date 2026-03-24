<?php
require '../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = $_POST['id'] ?? null;
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = trim($_POST['preco']);
    $estoque = trim($_POST['estoque']);
    $imagem = null;

    // Verificar se enviou uma nova imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $nomeOriginal = $_FILES['imagem']['name'];
        $imagem = time() . '-' . $nomeOriginal;
        $destino = $_SERVER['DOCUMENT_ROOT'] . '/clothing/assets/img/' . $imagem;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
    }

    if ($id) {
        // Edição
        // Buscar imagem atual para manter se não enviou nova
        $stmt = $pdo->prepare("SELECT url_imagem FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $produtoAtual = $stmt->fetch();
        $imagemAtual = $produtoAtual['url_imagem'];

        $sql = "UPDATE produtos SET nome=?, descricao=?, preco=?, estoque=?, url_imagem=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nome,
            $descricao,
            $preco,
            $estoque,
            $imagem ?: $imagemAtual,
            $id
        ]);

    } else {
        // Cadastro
        $sql = "INSERT INTO produtos (nome, descricao, preco, estoque, url_imagem) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nome,
            $descricao,
            $preco,
            $estoque,
            $imagem
        ]);
    }

    header('Location: index.php');
    exit;

} else {
    echo "❌ Erro: método inválido.";
}
?>
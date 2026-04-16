<?php
require '../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = $_POST['id'] ?? null;
    $nome = trim($_POST['nome']);
    $preco = trim($_POST['preco']);
    $cor = trim($_POST['cor']);
    $tamanho = trim($_POST['tamanho']);
    $estoque = trim($_POST['estoque']);
    $imagem = null;
    $descricao = trim($_POST['descricao']);
    $categoria_id = (int) trim($_POST['categoria_id']);

    // Verificar se enviou uma nova imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $nomeOriginal = $_FILES['imagem']['name'];
        $imagem = time() . '-' . $nomeOriginal;
        $destino = $_SERVER['DOCUMENT_ROOT'] . '/clothing/uploads/' . $imagem;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
    }

    if ($id) {
        // Edição
        // Buscar imagem atual para manter se não enviou nova
        $stmt = $pdo->prepare("SELECT url_imagem FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $produtoAtual = $stmt->fetch();
        $imagemAtual = $produtoAtual['url_imagem'];

        $sql = "UPDATE produtos SET nome=?, descricao=?, preco=?, cor=?, tamanho=?, estoque=?, url_imagem=?, categoria_id=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nome,
            $descricao,
            $preco,
            $cor,
            $tamanho,
            $estoque,
            $imagem ?: $imagemAtual,
            $categoria_id,
            $id
            
        ]);

    } else {
        // Cadastro
        $sql = "INSERT INTO produtos (nome, descricao, preco, cor, tamanho, estoque, url_imagem, categoria_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nome,
            $descricao,
            $preco,
            $cor,
            $tamanho,
            $estoque,
            $imagem,
            $categoria_id
        ]);
    }

    header('Location: index.php');
    exit;

} else {
    echo "❌ Erro: método inválido.";
}
?>
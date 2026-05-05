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
        $nomeOriginal = basename($_FILES['imagem']['name']);
        $imagem = 'uploads/' . $nomeOriginal;
        $pastaUploads = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        $destino = $pastaUploads . $nomeOriginal;

        $sqlImagemExistente = "SELECT COUNT(*) FROM produtos WHERE url_imagem IN (?, ?)";
        $parametrosImagemExistente = [$nomeOriginal, $imagem];

        if ($id) {
            $sqlImagemExistente .= " AND id <> ?";
            $parametrosImagemExistente[] = $id;
        }

        $stmt = $pdo->prepare($sqlImagemExistente);
        $stmt->execute($parametrosImagemExistente);
        $imagemJaCadastrada = (int) $stmt->fetchColumn() > 0;

        if (file_exists($destino) || $imagemJaCadastrada) {
            echo "Erro: ja existe uma imagem com esse nome. Renomeie o arquivo e tente novamente.";
            exit;
        }

        if (!is_dir($pastaUploads) || !move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
            echo "Erro: nao foi possivel salvar a imagem na pasta uploads.";
            exit;
        }
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

<?php
require '../../config/conexao.php';

function normalizarCaminhoImagemSalva(?string $caminho): ?string
{
    $caminho = trim((string) $caminho);

    if ($caminho === '') {
        return null;
    }

    if (
        str_starts_with($caminho, 'http://') ||
        str_starts_with($caminho, 'https://') ||
        str_starts_with($caminho, 'uploads/') ||
        str_starts_with($caminho, 'assets/')
    ) {
        return $caminho;
    }

    return 'uploads/' . ltrim($caminho, '/');
}

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
        $nomeArquivo = time() . '-' . $nomeOriginal;
        $imagem = 'uploads/' . $nomeArquivo;
        $pastaUploads = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        $destino = $pastaUploads . $nomeArquivo;

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
        $imagemAtual = normalizarCaminhoImagemSalva($produtoAtual['url_imagem'] ?? null);

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

<?php
require '../../config/conexao.php';

// Buscar categorias
$stmt = $pdo->query("SELECT id, nome FROM categorias");
$categorias = $stmt->fetchAll();

// Inicializar variáveis padrão (para cadastro)
$produto = [
    'id' => '',
    'nome' => '',
    'preco' => '',
    'cor' => '',
    'tamanho' => '',
    'estoque' => '',
    'imagem' => '',
    'categoria_id' => '',
    'descricao' => ''
];

$produtoIdAtual = null;

// Se houver id na URL, buscar produto existente
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $produtoIdAtual = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$produtoIdAtual]);
    $produtoBanco = $stmt->fetch();

    if ($produtoBanco) {
        $produto = $produtoBanco;
    }
}

$sqlImagens = "SELECT url_imagem FROM produtos WHERE url_imagem IS NOT NULL AND url_imagem <> ''";
$parametrosImagens = [];

if ($produtoIdAtual) {
    $sqlImagens .= " AND id <> ?";
    $parametrosImagens[] = $produtoIdAtual;
}

$stmt = $pdo->prepare($sqlImagens);
$stmt->execute($parametrosImagens);
$nomesImagensExistentes = [];

foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $urlImagem) {
    $nomeImagem = basename(str_replace('\\', '/', $urlImagem));

    if ($nomeImagem !== '') {
        $nomesImagensExistentes[] = mb_strtolower($nomeImagem, 'UTF-8');
    }
}

$pastaUploads = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'uploads';

if (is_dir($pastaUploads)) {
    foreach (scandir($pastaUploads) as $arquivoUpload) {
        if ($arquivoUpload !== '.' && $arquivoUpload !== '..' && is_file($pastaUploads . DIRECTORY_SEPARATOR . $arquivoUpload)) {
            $nomesImagensExistentes[] = mb_strtolower($arquivoUpload, 'UTF-8');
        }
    }
}

$nomesImagensExistentes = array_values(array_unique($nomesImagensExistentes));
?>

<h1 class="mb-4">Adicionar Produto</h1>
<form action="salvar.php" method="post" enctype="multipart/form-data" id="cadastroProduto">
    <?php if(!empty($produto['id'])): ?>
        <input type="hidden" name="id" value="<?= $produto['id'] ?>">
    <?php endif; ?>

    <div class="row">
        <div class="mb-3 col-md-4">
            <label class="form-label">Nome do produto</label>
            <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($produto['nome']) ?>">
        </div>
    
    <div class="mb-3 col-md-2">
    <label for="cor" class="form-label">
        Cor
    </label>

    <select 
        name="cor" 
        id="cor" 
        class="form-select" 
        required
    >

        <option value="">
            Selecione uma cor
        </option>

        <option value="Vermelho">
            🔴 Vermelho
        </option>

        <option value="Preto">
            ⚫ Preto
        </option>

        <option value="Branco">
            ⚪ Branco
        </option>

        <option value="Amarelo">
            🟡 Amarelo
        </option>

        <option value="Verde">
            🟢 Verde
        </option>

        <option value="Azul">
            🔵 Azul
        </option>

    </select>

</div>
        <div class="mb-3 col-md-2">
            <label class="form-label">Preço</label>
            <input type="number" name="preco" step="0.01" class="form-control" required value="<?= $produto['preco'] ?>">
        </div>
        <div class="mb-3 col-md-4">
            <label class="form-label">Estoque</label>
            <input type="number" name="estoque" class="form-control" required value="<?= $produto['estoque'] ?>">
        </div>
        <div class="mb-3 col-md-4">
            <label for="formFileSm" class="form-label">Adicione uma imagem</label>
            <input
                name="imagem"
                class="form-control form-control-sm"
                id="formFileSm"
                type="file"
                data-existing-images='<?= htmlspecialchars(json_encode($nomesImagensExistentes), ENT_QUOTES, 'UTF-8') ?>'
                <?= empty($produto['id']) ? 'required' : '' ?>
            >
            <div class="invalid-feedback">Ja existe uma imagem com esse nome. Renomeie o arquivo antes de cadastrar.</div>
            <?php if(!empty($produto['url_imagem'])): ?>
                <small>Imagem atual: <?= htmlspecialchars($produto['url_imagem']) ?></small>
            <?php endif; ?>
        </div>
        <div class="mb-3 col-md-4">
            <label class="form-label">Tamanho</label>
            <select name="tamanho" id="tamanho" class="form-control" required>
                <option value="" disabled>Selecione um tamanho</option>
                <?php 
                    $tamanhos = ['PP', 'P', 'M', 'G', 'GG'];
                    foreach($tamanhos as $tamanho): 
                ?>
                    <option value="<?= $tamanho ?>" <?= ($produto['tamanho'] == $tamanho) ? 'selected' : '' ?>>
                        <?= $tamanho ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3 col-md-4">
            <label class="form-label">Categoria</label>
            <select name="categoria_id" id="categoria" class="form-control" required>
            <option value="" disabled>Selecione uma categoria</option>
            <?php foreach($categorias as $categoria): ?>
                <option value="<?= $categoria['id'] ?>" <?= ($produto['categoria_id'] == $categoria['id']) ? 'selected' : '' ?>>
                    <?= $categoria['nome'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control" rows="9" required><?= htmlspecialchars($produto['descricao']) ?></textarea>
    </div>        

    <button type="submit" class="btn btn-primary"><?= empty($produto['id']) ? 'Cadastrar' : 'Atualizar' ?></button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>

<script type="module" src="../../assets/js/funcoesGlobais/cadastroPage.js"></script>

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

        <option value="Vermelho" <?= ($produto['cor'] == 'Vermelho') ? 'selected' : '' ?>>
            🔴 Vermelho
        </option>

        <option value="Preto" <?= ($produto['cor'] == 'Preto') ? 'selected' : '' ?>>
            ⚫ Preto
        </option>

        <option value="Branco" <?= ($produto['cor'] == 'Branco') ? 'selected' : '' ?>>
            ⚪ Branco
        </option>

        <option value="Amarelo" <?= ($produto['cor'] == 'Amarelo') ? 'selected' : '' ?>>
            🟡 Amarelo
        </option>

        <option value="Verde" <?= ($produto['cor'] == 'Verde') ? 'selected' : '' ?>>
            🟢 Verde
        </option>

        <option value="Azul" <?= ($produto['cor'] == 'Azul') ? 'selected' : '' ?>>
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
                <?= empty($produto['id']) ? 'required' : '' ?>
            >
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

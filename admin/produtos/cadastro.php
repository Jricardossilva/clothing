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
    'estoque' => '',
    'imagem' => '',
    'categoria_id' => '',
    'descricao' => ''
];

// Se houver id na URL, buscar produto existente
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $produtoBanco = $stmt->fetch();

    if ($produtoBanco) {
        $produto = $produtoBanco;
    }
}
?>

<h1 class="mb-4">Adicionar Produto</h1>
<form action="salvar.php" method="post" enctype="multipart/form-data">
    <?php if(!empty($produto['id'])): ?>
        <input type="hidden" name="id" value="<?= $produto['id'] ?>">
    <?php endif; ?>

    <div class="row">
        <div class="mb-3 col-md-4">
            <label class="form-label">Nome do produto</label>
            <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($produto['nome']) ?>">
        </div>
        <div class="mb-3 col-md-4">
            <label class="form-label">Preço</label>
            <input type="number" name="preco" step="0.01" class="form-control" required value="<?= $produto['preco'] ?>">
        </div>
        <div class="mb-3 col-md-4">
            <label class="form-label">Estoque</label>
            <input type="number" name="estoque" class="form-control" required value="<?= $produto['estoque'] ?>">
        </div>
        <div class="mb-3 col-md-6">
            <label for="formFileSm" class="form-label">Adicione uma imagem</label>
            <input name="imagem" class="form-control form-control-sm" id="formFileSm" type="file">
            <?php if(!empty($produto['imagem'])): ?>
                <small>Imagem atual: <?= $produto['imagem'] ?></small>
            <?php endif; ?>
        </div>
        <div class="mb-3 col-md-6">
            <label class="form-label">Categoria</label>
            <select name="categoria" id="categoria" class="form-control" required>
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
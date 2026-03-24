<?php
require '../../config/conexao.php';

// Inicializar variáveis padrão (para cadastro)
$categoria = [
    'id' => '',
    'nome' => '',
    'descricao' => '',
    'genero' => '',
];

// Se houver id na URL, buscar categoria existente
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $categoriaBanco = $stmt->fetch();

    if ($categoriaBanco) {
        $categoria = $categoriaBanco;
    }
}
?>

<h1>Editar Categoria</h1>
<form action="salvar.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
    <div class="form-group">
        <label for="nome">Nome:</label>
        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $categoria['nome']; ?>">
    </div>
    <div class="form-group">
        <label for="descricao">Descrição:</label>
        <input type="text" class="form-control" id="descricao" name="descricao" value="<?php echo $categoria['descricao']; ?>">
    </div>
    <div class="form-group">
        <label for="genero">Gênero:</label>
        <select class="form-select" aria-label="Default select example" name="genero" id="genero" required>
            <option selected disabled>Selecione o gênero</option>
            <option value="Masculino" <?php echo $categoria['genero'] === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
            <option value="Feminino" <?php echo $categoria['genero'] === 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
            <option value="Unissex" <?php echo $categoria['genero'] === 'Unissex' ? 'selected' : ''; ?>>Unissex</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary mt-3"><?= empty($categoria['id']) ? 'Cadastrar' : 'Atualizar' ?></button>
    <a href="index.php" class="btn btn-secondary mt-3">Cancelar</a>
</form>
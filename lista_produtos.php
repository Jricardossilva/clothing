<?php
include 'config/conexao.php';

$produtos = [];

if (isset($pdo)) {
    $stmt = $pdo->query('SELECT id, nome, preco, url_imagem FROM produtos ORDER BY id DESC');
    $produtos = $stmt->fetchAll();
}

function formatarPreco(float $preco): string
{
    return number_format($preco, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lista de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/lista_produtos.css" />
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <main>
        <h2 class="text-center mt-4 fw-bold">NOSSOS PRODUTOS</h2>

        <div class="container d-flex mt-4">
            <aside class="sidebar">
                <h3>Ordenar por</h3>
                <label><input type="radio" name="ordenar"> Menor preco</label>
                <label><input type="radio" name="ordenar"> Maior preco</label>
                <label><input type="radio" name="ordenar"> Mais vendidos</label>
                <label><input type="radio" name="ordenar"> Lancamentos</label>

                <h3>Tamanho</h3>
                <label><input type="checkbox" name="tamanho"> P</label>
                <label><input type="checkbox" name="tamanho"> M</label>
                <label><input type="checkbox" name="tamanho"> G</label>
                <label><input type="checkbox" name="tamanho"> GG</label>

                <h3>Cor</h3>
                <label><input type="checkbox" name="cor"> Branco</label>
                <label><input type="checkbox" name="cor"> Preto</label>
                <label><input type="checkbox" name="cor"> Azul</label>
                <label><input type="checkbox" name="cor"> Cinza</label>
            </aside>

            <section class="products">
                <?php if ($produtos): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <?php $imagem = !empty($produto['url_imagem']) ? $produto['url_imagem'] : 'assets/img/camiseta-preta.jpg'; ?>
                        <a href="produto.php?id=<?php echo (int) $produto['id']; ?>" class="product-link">
                            <div class="product">
                                <img src="<?php echo htmlspecialchars($imagem, ENT_QUOTES, 'UTF-8'); ?>"
                                    alt="<?php echo htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?>" />
                                <div class="name"><?php echo htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="price">R$ <?php echo formatarPreco((float) $produto['preco']); ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="product product-empty">
                        <img src="assets/img/camiseta-preta.jpg" alt="Nenhum produto cadastrado" />
                        <div class="name">Nenhum produto cadastrado</div>
                        <div class="price">Cadastre produtos no banco para listar aqui.</div>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>

<?php
include 'config/conexao.php';

$produtos = [];
$generoSelecionado = null;
$categoriaSelecionada = null;
$categoriaSelecionadaNome = null;
$tituloPagina = 'NOSSOS PRODUTOS';
$ordenacaoSelecionada = 'lancamentos';

$mapaGeneros = [
    'masculino' => ['%Masculino%', '%Masculina%'],
    'feminino' => ['%Feminino%', '%Feminina%'],
];

$mapaOrdenacao = [
    'menor_preco' => [
        'label' => 'Menor preço',
        'sql' => 'p.preco ASC, p.id DESC',
    ],
    'maior_preco' => [
        'label' => 'Maior preço',
        'sql' => 'p.preco DESC, p.id DESC',
    ],
    'lancamentos' => [
        'label' => 'Lançamentos',
        'sql' => 'p.id DESC',
    ],
];

$generoParam = strtolower(trim((string) filter_input(INPUT_GET, 'genero', FILTER_UNSAFE_RAW)));
$ordenarParam = strtolower(trim((string) filter_input(INPUT_GET, 'ordenar', FILTER_UNSAFE_RAW)));
$categoriaParam = filter_input(
    INPUT_GET,
    'categoria',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (array_key_exists($generoParam, $mapaGeneros)) {
    $generoSelecionado = $generoParam;
    $tituloPagina = 'PRODUTOS ' . strtoupper($generoSelecionado);
}

if (array_key_exists($ordenarParam, $mapaOrdenacao)) {
    $ordenacaoSelecionada = $ordenarParam;
}

$orderBy = $mapaOrdenacao[$ordenacaoSelecionada]['sql'];

if (isset($pdo)) {
    if ($categoriaParam !== false && $categoriaParam !== null) {
        $stmtCategoria = $pdo->prepare(
            'SELECT id, nome, genero
             FROM categorias
             WHERE id = :categoria_id'
        );
        $stmtCategoria->execute([':categoria_id' => $categoriaParam]);
        $categoriaBanco = $stmtCategoria->fetch();

        if ($categoriaBanco) {
            $categoriaSelecionada = (int) $categoriaBanco['id'];
            $categoriaSelecionadaNome = $categoriaBanco['nome'];

            if ($generoSelecionado === null && !empty($categoriaBanco['genero'])) {
                $generoBanco = strtolower((string) $categoriaBanco['genero']);
                if (array_key_exists($generoBanco, $mapaGeneros)) {
                    $generoSelecionado = $generoBanco;
                }
            }
        }
    }

    if ($categoriaSelecionadaNome !== null && $generoSelecionado !== null) {
        $tituloPagina = strtoupper($categoriaSelecionadaNome) . ' ' . strtoupper($generoSelecionado);
    } elseif ($categoriaSelecionadaNome !== null) {
        $tituloPagina = strtoupper($categoriaSelecionadaNome);
    } elseif ($generoSelecionado !== null) {
        $tituloPagina = 'PRODUTOS ' . strtoupper($generoSelecionado);
    }

    $where = [
        'p.situacao = 1',
        'COALESCE(p.estoque, 1) > 0',
    ];
    $parametros = [];

    if ($generoSelecionado !== null) {
        $where[] = '(LOWER(COALESCE(c.genero, "")) = :genero
            OR p.nome LIKE :termo1
            OR p.nome LIKE :termo2)';
        $parametros[':genero'] = $generoSelecionado;
        $parametros[':termo1'] = $mapaGeneros[$generoSelecionado][0];
        $parametros[':termo2'] = $mapaGeneros[$generoSelecionado][1];
    }

    if ($categoriaSelecionada !== null) {
        $where[] = 'c.id = :categoria_id';
        $parametros[':categoria_id'] = $categoriaSelecionada;
    }

    $sql = 'SELECT p.id, p.nome, p.preco, p.url_imagem, c.genero AS categoria_genero, c.nome AS categoria_nome
            FROM produtos p
            LEFT JOIN categorias c
              ON c.id = p.categoria_id
            WHERE ' . implode(' AND ', $where) . '
            ORDER BY ' . $orderBy;

    if ($parametros) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametros);
    } else {
        $stmt = $pdo->query($sql);
    }

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
        <h2 class="text-center mt-4 fw-bold"><?php echo htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8'); ?></h2>

        <div class="container d-flex mt-4">
            <aside class="sidebar">
                <form method="get" action="lista_produtos.php">
                    <?php if ($generoSelecionado !== null): ?>
                        <input type="hidden" name="genero" value="<?php echo htmlspecialchars($generoSelecionado, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endif; ?>
                    <?php if ($categoriaSelecionada !== null): ?>
                        <input type="hidden" name="categoria" value="<?php echo (int) $categoriaSelecionada; ?>">
                    <?php endif; ?>

                <h3>Ordenar por</h3>
                    <?php foreach ($mapaOrdenacao as $valorOrdenacao => $dadosOrdenacao): ?>
                        <label>
                            <input
                                type="radio"
                                name="ordenar"
                                value="<?php echo htmlspecialchars($valorOrdenacao, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo $ordenacaoSelecionada === $valorOrdenacao ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars($dadosOrdenacao['label'], ENT_QUOTES, 'UTF-8'); ?>
                        </label>
                    <?php endforeach; ?>

                <h3>Tamanho</h3>
                <label><input type="checkbox" name="tamanho"> PP</label>
                <label><input type="checkbox" name="tamanho"> P</label>
                <label><input type="checkbox" name="tamanho"> M</label>
                <label><input type="checkbox" name="tamanho"> G</label>
                <label><input type="checkbox" name="tamanho"> GG</label>

                <h3>Cor</h3>
                <label><input type="checkbox" name="cor"> Branco</label>
                <label><input type="checkbox" name="cor"> Preto</label>
                <label><input type="checkbox" name="cor"> Cinza</label>
                <label><input type="checkbox" name="cor"> Azul</label>
                <label><input type="checkbox" name="cor"> Vermelho</label>
                <label><input type="checkbox" name="cor"> Verde</label>
                <label><input type="checkbox" name="cor"> Rosa</label>

                    <button type="submit" class="btn btn-dark w-100 mt-3">Aplicar Filtros</button>
                </form>
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
                        <div class="name">Nenhum produto encontrado</div>
                        <div class="price">Nao ha produtos para o filtro selecionado.</div>
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

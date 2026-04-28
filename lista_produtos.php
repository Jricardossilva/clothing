<?php
include 'config/conexao.php';

$produtos = [];
$generoSelecionado = null;
$categoriaSelecionada = null;
$categoriaSelecionadaNome = null;
$tituloPagina = 'NOSSOS PRODUTOS';
$ordenacaoSelecionada = 'lancamentos';
$tamanhosDisponiveis = ['PP', 'P', 'M', 'G', 'GG', 'XG'];
$coresDisponiveis = [];
$tamanhosSelecionados = [];
$coresSelecionadas = [];
$parametrosLimparFiltros = [];

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

function obterFiltroArray(string $nome): array
{
    $valor = $_GET[$nome] ?? [];

    if (!is_array($valor)) {
        $valor = [$valor];
    }

    $valor = array_map(
        static fn($item): string => trim((string) $item),
        $valor
    );

    return array_values(array_unique(array_filter(
        $valor,
        static fn(string $item): bool => $item !== ''
    )));
}

$generoParam = strtolower(trim((string) filter_input(INPUT_GET, 'genero', FILTER_UNSAFE_RAW)));
$ordenarParam = strtolower(trim((string) filter_input(INPUT_GET, 'ordenar', FILTER_UNSAFE_RAW)));
$tamanhosParam = obterFiltroArray('tamanho');
$coresParam = obterFiltroArray('cor');
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
    $stmtTamanhos = $pdo->query(
        'SELECT DISTINCT tamanho
         FROM produtos
         WHERE tamanho IS NOT NULL
           AND TRIM(tamanho) <> ""
         ORDER BY FIELD(tamanho, "PP", "P", "M", "G", "GG", "XG"), tamanho'
    );
    $tamanhosBanco = array_values(array_filter(array_map(
        static fn($tamanho): string => strtoupper(trim((string) $tamanho['tamanho'])),
        $stmtTamanhos->fetchAll()
    )));

    if ($tamanhosBanco) {
        $tamanhosDisponiveis = $tamanhosBanco;
    }

    $stmtCores = $pdo->query(
        'SELECT DISTINCT cor
         FROM produtos
         WHERE cor IS NOT NULL
           AND TRIM(cor) <> ""
         ORDER BY cor ASC'
    );
    $coresDisponiveis = array_values(array_filter(array_map(
        static fn($cor): string => trim((string) $cor['cor']),
        $stmtCores->fetchAll()
    )));

    $mapaTamanhosDisponiveis = array_flip($tamanhosDisponiveis);
    $tamanhosSelecionados = array_values(array_filter(array_map(
        static fn(string $tamanho): string => strtoupper($tamanho),
        $tamanhosParam
    ), static fn(string $tamanho): bool => isset($mapaTamanhosDisponiveis[$tamanho])));

    $mapaCoresDisponiveis = [];
    foreach ($coresDisponiveis as $corDisponivel) {
        $mapaCoresDisponiveis[strtolower(trim($corDisponivel))] = $corDisponivel;
    }

    foreach ($coresParam as $corParam) {
        $corNormalizada = strtolower(trim($corParam));
        if (isset($mapaCoresDisponiveis[$corNormalizada])) {
            $coresSelecionadas[] = $mapaCoresDisponiveis[$corNormalizada];
        }
    }
    $coresSelecionadas = array_values(array_unique($coresSelecionadas));

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

    if ($generoSelecionado !== null) {
        $parametrosLimparFiltros['genero'] = $generoSelecionado;
    }

    if ($categoriaSelecionada !== null) {
        $parametrosLimparFiltros['categoria'] = $categoriaSelecionada;
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

    if ($tamanhosSelecionados) {
        $placeholdersTamanhos = [];

        foreach ($tamanhosSelecionados as $indice => $tamanhoSelecionado) {
            $placeholder = ':tamanho' . $indice;
            $placeholdersTamanhos[] = $placeholder;
            $parametros[$placeholder] = $tamanhoSelecionado;
        }

        $where[] = 'p.tamanho IN (' . implode(', ', $placeholdersTamanhos) . ')';
    }

    if ($coresSelecionadas) {
        $placeholdersCores = [];

        foreach ($coresSelecionadas as $indice => $corSelecionada) {
            $placeholder = ':cor' . $indice;
            $placeholdersCores[] = $placeholder;
            $parametros[$placeholder] = strtolower(trim($corSelecionada));
        }

        $where[] = 'TRIM(LOWER(p.cor)) IN (' . implode(', ', $placeholdersCores) . ')';
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

function formatarNomeCor(string $cor): string
{
    $corTratada = strtoupper(trim($cor));
    $mapaCores = [
        '#FFFFFF' => 'Branco',
        '#FFF' => 'Branco',
        '#000000' => 'Preto',
        '#000' => 'Preto',
        '#808080' => 'Cinza',
        '#C0C0C0' => 'Cinza claro',
        '#FF0000' => 'Vermelho',
        '#F00' => 'Vermelho',
        '#0000FF' => 'Azul',
        '#00F' => 'Azul',
        '#008000' => 'Verde',
        '#00FF00' => 'Verde',
        '#0F0' => 'Verde',
        '#FFFF00' => 'Amarelo',
        '#FF0' => 'Amarelo',
        '#FFA500' => 'Laranja',
        '#FFC0CB' => 'Rosa',
        '#800080' => 'Roxo',
        '#A52A2A' => 'Marrom',
        '#F5F5DC' => 'Bege',
    ];

    return $mapaCores[$corTratada] ?? $cor;
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
        <div class="product-list-shell">
            <h2 class="text-center mt-4 fw-bold product-list-title"><?php echo htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8'); ?></h2>

            <div class="product-list-toolbar d-lg-none">
                <button
                    class="btn btn-dark product-list-toolbar-btn"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#filtrosListaProdutos"
                    aria-controls="filtrosListaProdutos">
                    Filtros
                </button>
                <div class="product-list-count">
                    <?php echo count($produtos); ?> produto<?php echo count($produtos) === 1 ? '' : 's'; ?>
                </div>
            </div>

            <div class="product-list-layout mt-4">
                <aside class="sidebar d-none d-lg-block">
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
                        <?php foreach ($tamanhosDisponiveis as $tamanhoDisponivel): ?>
                            <label>
                                <input
                                    type="checkbox"
                                    name="tamanho[]"
                                    value="<?php echo htmlspecialchars($tamanhoDisponivel, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo in_array($tamanhoDisponivel, $tamanhosSelecionados, true) ? 'checked' : ''; ?>>
                                <?php echo htmlspecialchars($tamanhoDisponivel, ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                        <?php endforeach; ?>

                        <h3>Cor</h3>
                        <?php if ($coresDisponiveis): ?>
                            <div class="filter-color-grid">
                                <?php foreach ($coresDisponiveis as $corDisponivel): ?>
                                    <label class="filter-color-option" title="<?php echo htmlspecialchars(formatarNomeCor($corDisponivel), ENT_QUOTES, 'UTF-8'); ?>">
                                        <input
                                            type="checkbox"
                                            name="cor[]"
                                            value="<?php echo htmlspecialchars($corDisponivel, ENT_QUOTES, 'UTF-8'); ?>"
                                            <?php echo in_array($corDisponivel, $coresSelecionadas, true) ? 'checked' : ''; ?>>
                                        <span
                                            class="filter-color-swatch"
                                            style="background-color: <?php echo htmlspecialchars($corDisponivel, ENT_QUOTES, 'UTF-8'); ?>;"
                                            aria-label="<?php echo htmlspecialchars(formatarNomeCor($corDisponivel), ENT_QUOTES, 'UTF-8'); ?>">
                                        </span>
                                        <span class="filter-color-name"><?php echo htmlspecialchars(formatarNomeCor($corDisponivel), ENT_QUOTES, 'UTF-8'); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="filter-empty">Nenhuma cor cadastrada.</p>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-dark w-100 mt-3">Aplicar Filtros</button>
                        <a
                            href="lista_produtos.php<?php echo $parametrosLimparFiltros ? '?' . htmlspecialchars(http_build_query($parametrosLimparFiltros), ENT_QUOTES, 'UTF-8') : ''; ?>"
                            class="btn btn-outline-secondary w-100 mt-2">
                            Limpar Filtros
                        </a>
                    </form>
                </aside>

                <div class="offcanvas offcanvas-start product-filter-offcanvas" tabindex="-1" id="filtrosListaProdutos" aria-labelledby="filtrosListaProdutosLabel">
                    <div class="offcanvas-header">
                        <div>
                            <p class="product-filter-eyebrow mb-1">Clothing</p>
                            <h5 class="offcanvas-title mb-0" id="filtrosListaProdutosLabel">Filtros</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
                    </div>
                    <div class="offcanvas-body">
                        <form method="get" action="lista_produtos.php" class="product-filter-form">
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
                            <?php foreach ($tamanhosDisponiveis as $tamanhoDisponivel): ?>
                                <label>
                                    <input
                                        type="checkbox"
                                        name="tamanho[]"
                                        value="<?php echo htmlspecialchars($tamanhoDisponivel, ENT_QUOTES, 'UTF-8'); ?>"
                                        <?php echo in_array($tamanhoDisponivel, $tamanhosSelecionados, true) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($tamanhoDisponivel, ENT_QUOTES, 'UTF-8'); ?>
                                </label>
                            <?php endforeach; ?>

                            <h3>Cor</h3>
                            <?php if ($coresDisponiveis): ?>
                                <div class="filter-color-grid">
                                    <?php foreach ($coresDisponiveis as $corDisponivel): ?>
                                        <label class="filter-color-option" title="<?php echo htmlspecialchars(formatarNomeCor($corDisponivel), ENT_QUOTES, 'UTF-8'); ?>">
                                            <input
                                                type="checkbox"
                                                name="cor[]"
                                                value="<?php echo htmlspecialchars($corDisponivel, ENT_QUOTES, 'UTF-8'); ?>"
                                                <?php echo in_array($corDisponivel, $coresSelecionadas, true) ? 'checked' : ''; ?>>
                                            <span
                                                class="filter-color-swatch"
                                                style="background-color: <?php echo htmlspecialchars($corDisponivel, ENT_QUOTES, 'UTF-8'); ?>;"
                                                aria-label="<?php echo htmlspecialchars(formatarNomeCor($corDisponivel), ENT_QUOTES, 'UTF-8'); ?>">
                                            </span>
                                            <span class="filter-color-name"><?php echo htmlspecialchars(formatarNomeCor($corDisponivel), ENT_QUOTES, 'UTF-8'); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="filter-empty">Nenhuma cor cadastrada.</p>
                            <?php endif; ?>

                            <div class="product-filter-actions">
                                <button type="submit" class="btn btn-dark w-100 mt-3">Aplicar Filtros</button>
                                <a
                                    href="lista_produtos.php<?php echo $parametrosLimparFiltros ? '?' . htmlspecialchars(http_build_query($parametrosLimparFiltros), ENT_QUOTES, 'UTF-8') : ''; ?>"
                                    class="btn btn-outline-secondary w-100 mt-2">
                                    Limpar Filtros
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <section class="products-wrapper">
                    <div class="products-header d-none d-lg-flex">
                        <div class="product-list-count">
                            <?php echo count($produtos); ?> produto<?php echo count($produtos) === 1 ? '' : 's'; ?>
                        </div>
                    </div>

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
                </section>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>

<?php
include 'config/conexao.php';

function formatarPrecoHome(float $preco): string
{
  return number_format($preco, 2, ',', '.');
}

function normalizarCaminhoImagem(?string $caminho): string
{
  if (empty($caminho)) {
    return 'assets/img/placeholder.png';
  }

  if (
    str_starts_with($caminho, 'http://') ||
    str_starts_with($caminho, 'https://') ||
    str_starts_with($caminho, 'uploads/') ||
    str_starts_with($caminho, 'assets/')
  ) {
    $caminhoNormalizado = $caminho;
  } else {
    $caminhoNormalizado = 'uploads/' . ltrim($caminho, '/');
  }

  if (
    !str_starts_with($caminhoNormalizado, 'http://') &&
    !str_starts_with($caminhoNormalizado, 'https://')
  ) {
    $arquivoLocal = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $caminhoNormalizado);
    if (!file_exists($arquivoLocal)) {
      return 'assets/img/placeholder.png';
    }
  }

  return $caminhoNormalizado;
}

function calcularPrecoOriginal(float $precoAtual): float
{
  return round($precoAtual * 1.35, 2);
}

function calcularPrecoKit(float $precoAtual): array
{
  $precoCheio = round($precoAtual * 3, 2);
  $precoPromocional = round($precoCheio * 0.85, 2);

  return [
    'cheio' => $precoCheio,
    'promocional' => $precoPromocional,
  ];
}

function embaralharProdutos(array $produtos): array
{
  shuffle($produtos);

  return $produtos;
}

function separarProdutosPorGenero(array $produtos): array
{
  $femininos = [];
  $masculinos = [];
  $neutros = [];

  foreach ($produtos as $produto) {
    $nomeProduto = mb_strtolower($produto['nome'], 'UTF-8');

    if (str_contains($nomeProduto, 'femin')) {
      $femininos[] = $produto;
    } elseif (str_contains($nomeProduto, 'mascul')) {
      $masculinos[] = $produto;
    } else {
      $neutros[] = $produto;
    }
  }

  return [
    'femininos' => embaralharProdutos($femininos),
    'masculinos' => embaralharProdutos($masculinos),
    'neutros' => embaralharProdutos($neutros),
  ];
}

function intercalarProdutosPorGenero(array $femininos, array $masculinos): array
{
  $resultado = [];
  $max = max(count($femininos), count($masculinos));

  for ($i = 0; $i < $max; $i++) {
    if (isset($femininos[$i])) $resultado[] = $femininos[$i];
    if (isset($masculinos[$i])) $resultado[] = $masculinos[$i];
  }

  return $resultado;
}

function obterProdutoAleatorio(array &$produtos, array $fallback): array
{
  return array_shift($produtos) ?? $fallback;
}

function removerProdutosPorIds(array $produtos, array $idsUsados): array
{
  if (empty($idsUsados)) {
    return $produtos;
  }

  return array_values(array_filter(
    $produtos,
    static fn(array $produto): bool => !in_array($produto['id'], $idsUsados, true)
  ));
}

/* =========================
   🔥 BUSCAR DO BANCO
========================= */
$produtosHome = [];

if (isset($pdo)) {
  $stmt = $pdo->query("
    SELECT
      p.id,
      p.nome,
      p.preco,
      COALESCE(p.url_imagem, pi.url_imagem, 'assets/img/placeholder.png') AS url_imagem
    FROM produtos p
    LEFT JOIN produto_imagem pi
      ON pi.produto_id = p.id
      AND pi.ordem = (
        SELECT MIN(pi2.ordem)
        FROM produto_imagem pi2
        WHERE pi2.produto_id = p.id
      )
    WHERE p.situacao = 1
      AND COALESCE(p.estoque, 1) > 0
    ORDER BY p.id DESC
  ");

  foreach ($stmt->fetchAll() as $produto) {
    $produtosHome[] = [
      'id' => (int) $produto['id'],
      'nome' => $produto['nome'],
      'preco' => (float) $produto['preco'],
      'imagem' => normalizarCaminhoImagem($produto['url_imagem'] ?? null),
      'alt' => $produto['nome'],
      'preco_original' => calcularPrecoOriginal((float) $produto['preco']),
    ];
  }
}

/* =========================
   🔁 LÓGICA EXISTENTE
========================= */
$produtosPorGenero = separarProdutosPorGenero($produtosHome);

$femininosHome = $produtosPorGenero['femininos'];
$masculinosHome = $produtosPorGenero['masculinos'];
$neutrosHome = $produtosPorGenero['neutros'];

$produtosHome = intercalarProdutosPorGenero($femininosHome, $masculinosHome);
$produtosHome = array_merge($produtosHome, $neutrosHome);

$precoInicial = !empty($produtosHome) ? min(array_column($produtosHome, 'preco')) : 0;

$primeiraVitrine = array_slice($produtosHome, 0, 4);
$segundaVitrine = array_slice($produtosHome, 4, 4);

$idsUsadosNasVitrines = array_column(array_merge($primeiraVitrine, $segundaVitrine), 'id');

$femininosDisponiveis = removerProdutosPorIds($femininosHome, $idsUsadosNasVitrines);
$masculinosDisponiveis = removerProdutosPorIds($masculinosHome, $idsUsadosNasVitrines);

$kitFeminino = obterProdutoAleatorio($femininosDisponiveis, []);
$kitMasculino = obterProdutoAleatorio($masculinosDisponiveis, []);

$looksPrimavera = intercalarProdutosPorGenero(
  array_slice($femininosDisponiveis, 0, 2),
  array_slice($masculinosDisponiveis, 0, 2)
);

$precosKitFeminino = calcularPrecoKit($kitFeminino['preco'] ?? 0);
$precosKitMasculino = calcularPrecoKit($kitMasculino['preco'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
  <link rel="stylesheet" href="assets/css/index.css" />
  <title>Clothing</title>
</head>

<body>
  <?php include 'includes/header.php'; ?>

  <main>
    <!-- Carrossel inicial -->
    <div id="carouselExampleInterval" class="carousel slide text-center mt-3 h-75" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active" data-bs-interval="2000">
          <img class="banner rounded-1 d-block w-100" src="assets/img/banner-1.jpg" alt="..." />
        </div>
        <div class="carousel-item" data-bs-interval="2000">
          <img class="banner rounded-1 d-block w-100" src="assets/img/banner-2.jpg" alt="..." />
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval"
        data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval"
        data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>

    <section class="mx-auto section-roupas">
      <h2 class="py-3 mt-3">Peças a partir de R$ <?php echo formatarPrecoHome($precoInicial); ?></h2>
      <div class="row row-cols-1 row-cols-md-4 g-4">
        <?php if ($primeiraVitrine): ?>
          <?php foreach ($primeiraVitrine as $produto): ?>
            <div class="col">
              <a href="produto.php?id=<?php echo (int) $produto['id']; ?>" class="text-decoration-none text-dark">
                <div class="card">
                  <img src="<?php echo htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top"
                    alt="<?php echo htmlspecialchars($produto['alt'], ENT_QUOTES, 'UTF-8'); ?>" />
                  <div class="card-body text-center">
                    <h5 class="card-title"></h5>
                    <p class="card-text">
                      <strong><?php echo htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </p>
                    <p class="d-flex justify-content-center gap-3">
                      <strong>R$ <?php echo formatarPrecoHome($produto['preco']); ?></strong>
                      <del>R$ <?php echo formatarPrecoHome($produto['preco_original']); ?></del>
                    </p>
                  </div>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <p class="text-center mb-0">Nenhum produto disponivel no momento.</p>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <div class="text-center mt-4">
      <button>
        <span class="box">Ver tudo !</span>
      </button>
    </div>

    <!-- Área de promoções -->
    <div class="text-center">
      <img class="img-fluid banner-promo mt-4 w-100" src="assets/img/banner-3.png" alt="" />
    </div>

    <section class="mx-auto section-roupas mt-5">
      <div class="row row-cols-1 row-cols-md-4 g-4">
        <?php foreach ($segundaVitrine as $produto): ?>
          <div class="col">
            <a href="produto.php?id=<?php echo (int) $produto['id']; ?>" class="text-decoration-none text-dark">
              <div class="card">
                <img src="<?php echo htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top"
                  alt="<?php echo htmlspecialchars($produto['alt'], ENT_QUOTES, 'UTF-8'); ?>" />
                <div class="card-body text-center">
                  <h5 class="card-title"></h5>
                  <p class="card-text">
                    <strong><?php echo htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?></strong>
                  </p>
                  <p class="d-flex justify-content-center gap-3">
                    <strong>R$ <?php echo formatarPrecoHome($produto['preco']); ?></strong>
                    <del>R$ <?php echo formatarPrecoHome($produto['preco_original']); ?></del>
                  </p>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <div class="text-center mt-4">
      <button>
        <span class="box">Ver tudo !</span>
      </button>
    </div>

    <!-- Área de kits -->
    <section class="bg-black p-1">
      <div class="d-flex">
        <h2
          class="m-4 px-2 border-start border-end border-2 border-white text-light text-uppercase text-center fs-4 montagem">
          Monte seu kit
        </h2>
      </div>
      <div class="d-flex w-100 gap-3 px-4 mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center bg-white w-50 p-3">
          <h4 class="mt-5 mb-3 text-uppercase">Kit Camisetas</h4>
          <h3 class="fs-1 fw-bold">Prima Feminino</h3>
          <p class="fs-2">de R$<?php echo formatarPrecoHome($precosKitFeminino['cheio']); ?> por
            R$<?php echo formatarPrecoHome($precosKitFeminino['promocional']); ?></p>
          <p class="fs-3 mt-1 text-decoration-underline">Cupom: KITPIMA</p>
          <img class="w-100" src="<?php echo htmlspecialchars($kitFeminino['imagem'] ?? 'assets/img/placeholder.png', ENT_QUOTES, 'UTF-8'); ?>"
            alt="<?php echo htmlspecialchars($kitFeminino['nome'] ?? 'Kit feminino', ENT_QUOTES, 'UTF-8'); ?>" />
          <a href="" class="my-3 fw-bold text-uppercase menu__link">Comprar</a>
        </div>
        <div class="d-flex flex-column align-items-center justify-content-center bg-white w-50">
          <h4 class="mt-5 mb-3 text-uppercase">Kit Camisetas</h4>
          <h3 class="fs-1 fw-bold">Prima Masculino</h3>
          <p class="fs-2">de R$<?php echo formatarPrecoHome($precosKitMasculino['cheio']); ?> por
            R$<?php echo formatarPrecoHome($precosKitMasculino['promocional']); ?></p>
          <p class="fs-3 text-decoration-underline mt-1">Cupom: KITPIMA</p>
          <img class="w-100" src="<?php echo htmlspecialchars($kitMasculino['imagem'] ?? 'assets/img/placeholder.png', ENT_QUOTES, 'UTF-8'); ?>"
            alt="<?php echo htmlspecialchars($kitMasculino['nome'] ?? 'Kit masculino', ENT_QUOTES, 'UTF-8'); ?>" />
          <a href="" class="my-3 fw-bold text-uppercase menu__link">Comprar</a>
        </div>
      </div>
      <div class="d-flex">
        <h2
          class="mx-4 mt-4 px-2 border-start border-end border-2 border-white text-light text-uppercase text-center fs-4 montagem">
          Monte seu look para a primavera
        </h2>
      </div>
      <div class="d-flex gap-4 p-3 mx-2">
        <?php foreach ($looksPrimavera as $look): ?>
          <div class="card w-25">
            <img src="<?php echo htmlspecialchars($look['imagem'], ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top"
              alt="<?php echo htmlspecialchars($look['nome'], ENT_QUOTES, 'UTF-8'); ?>" />
            <div class="card-body">
              <p class="card-text text-center">
                <a href="#" class="fs-5 menu__link">Comprar</a>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <?php include 'includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>
  <script src="assets/js/index.js"></script>
</body>

</html>

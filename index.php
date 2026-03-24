<?php
include 'config/conexao.php';

function formatarPrecoHome(float $preco): string
{
  return number_format($preco, 2, ',', '.');
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

  foreach ($produtos as $produto) {
    if (($produto['genero'] ?? null) === 'feminino') {
      $femininos[] = $produto;
      continue;
    }

    $masculinos[] = $produto;
  }

  return [
    'femininos' => embaralharProdutos($femininos),
    'masculinos' => embaralharProdutos($masculinos),
  ];
}

function intercalarProdutosPorGenero(array $femininos, array $masculinos): array
{
  $produtosIntercalados = [];
  $maiorGrupo = max(count($femininos), count($masculinos));

  for ($indice = 0; $indice < $maiorGrupo; $indice++) {
    if (isset($femininos[$indice])) {
      $produtosIntercalados[] = $femininos[$indice];
    }

    if (isset($masculinos[$indice])) {
      $produtosIntercalados[] = $masculinos[$indice];
    }
  }

  return $produtosIntercalados;
}

function obterProdutoAleatorio(array &$produtos, array $fallback): array
{
  $produto = array_shift($produtos);

  if ($produto === null) {
    return $fallback;
  }

  return $produto;
}

$produtosHome = [
  [
    'imagem' => 'uploads/feminino1.png',
    'alt' => 'Camiseta feminina',
    'genero' => 'feminino',
    'nome' => 'Camiseta Feminina 1',
    'preco' => 59.90,
  ],
  [
    'imagem' => 'uploads/masculino2.png',
    'alt' => 'Camiseta masculina',
    'genero' => 'masculino',
    'nome' => 'Camiseta Masculina 2',
    'preco' => 73.90,
  ],
  [
    'imagem' => 'uploads/feminino3.png',
    'alt' => 'Camiseta feminina',
    'genero' => 'feminino',
    'nome' => 'Camiseta Feminina 3',
    'preco' => 72.50,
  ],
  [
    'imagem' => 'uploads/masculino4.png',
    'alt' => 'Camiseta masculina',
    'genero' => 'masculino',
    'nome' => 'Camiseta Masculina 4',
    'preco' => 84.90,
  ],
  [
    'imagem' => 'uploads/feminino5.png',
    'alt' => 'Camiseta feminina',
    'genero' => 'feminino',
    'nome' => 'Camiseta Feminina 5',
    'preco' => 83.40,
  ],
  [
    'imagem' => 'uploads/masculino6.png',
    'alt' => 'Camiseta masculina',
    'genero' => 'masculino',
    'nome' => 'Camiseta Masculina 6',
    'preco' => 76.40,
  ],
  [
    'imagem' => 'uploads/feminino7.png',
    'alt' => 'Camiseta feminina',
    'genero' => 'feminino',
    'nome' => 'Camiseta Feminina 7',
    'preco' => 57.90,
  ],
  [
    'imagem' => 'uploads/masculino8.png',
    'alt' => 'Camiseta masculina',
    'genero' => 'masculino',
    'nome' => 'Camiseta Masculina 8',
    'preco' => 87.50,
  ],
];

$produtosPorImagem = [];
$produtosPorNome = [];

if (isset($pdo)) {
  $imagens = array_column($produtosHome, 'imagem');
  $nomes = array_column($produtosHome, 'nome');
  $placeholdersImagens = implode(',', array_fill(0, count($imagens), '?'));
  $placeholdersNomes = implode(',', array_fill(0, count($nomes), '?'));

  $stmt = $pdo->prepare(
    "SELECT nome, preco, url_imagem
     FROM produtos
     WHERE url_imagem IN ($placeholdersImagens) OR nome IN ($placeholdersNomes)"
  );
  $stmt->execute([...$imagens, ...$nomes]);

  foreach ($stmt->fetchAll() as $produto) {
    if (!empty($produto['url_imagem'])) {
      $produtosPorImagem[$produto['url_imagem']] = $produto;
    }

    if (!empty($produto['nome'])) {
      $produtosPorNome[$produto['nome']] = $produto;
    }
  }
}

foreach ($produtosHome as $indice => $produtoHome) {
  $produtoBanco = $produtosPorImagem[$produtoHome['imagem']] ?? $produtosPorNome[$produtoHome['nome']] ?? null;

  if ($produtoBanco) {
    $produtosHome[$indice]['nome'] = $produtoBanco['nome'];
    $produtosHome[$indice]['preco'] = (float) $produtoBanco['preco'];
  }

  $produtosHome[$indice]['preco_original'] = calcularPrecoOriginal($produtosHome[$indice]['preco']);
}

$produtosPorGenero = separarProdutosPorGenero($produtosHome);
$femininosHome = $produtosPorGenero['femininos'];
$masculinosHome = $produtosPorGenero['masculinos'];
$produtosHome = intercalarProdutosPorGenero($femininosHome, $masculinosHome);
$precoInicial = min(array_column($produtosHome, 'preco'));
$primeiraVitrine = array_slice($produtosHome, 0, 4);
$segundaVitrine = array_slice($produtosHome, 4, 4);

$kitFeminino = obterProdutoAleatorio($femininosHome, [
  'imagem' => 'uploads/feminino9.png',
  'alt' => 'Kit feminino',
  'genero' => 'feminino',
  'nome' => 'Camiseta Feminina 9',
  'preco' => 74.90,
]);

$kitMasculino = obterProdutoAleatorio($masculinosHome, [
  'imagem' => 'uploads/masculino1.png',
  'alt' => 'Kit masculino',
  'genero' => 'masculino',
  'nome' => 'Camiseta Masculina 1',
  'preco' => 61.90,
]);

$looksPrimavera = intercalarProdutosPorGenero(
  array_slice($femininosHome, 0, 2),
  array_slice($masculinosHome, 0, 2)
);

$precosKitFeminino = calcularPrecoKit($kitFeminino['preco']);
$precosKitMasculino = calcularPrecoKit($kitMasculino['preco']);
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
        <?php foreach ($primeiraVitrine as $produto): ?>
          <div class="col">
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
          </div>
        <?php endforeach; ?>
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
          <img class="w-100" src="<?php echo htmlspecialchars($kitFeminino['imagem'], ENT_QUOTES, 'UTF-8'); ?>"
            alt="<?php echo htmlspecialchars($kitFeminino['nome'], ENT_QUOTES, 'UTF-8'); ?>" />
          <a href="" class="my-3 fw-bold text-uppercase menu__link">Comprar</a>
        </div>
        <div class="d-flex flex-column align-items-center justify-content-center bg-white w-50">
          <h4 class="mt-5 mb-3 text-uppercase">Kit Camisetas</h4>
          <h3 class="fs-1 fw-bold">Prima Masculino</h3>
          <p class="fs-2">de R$<?php echo formatarPrecoHome($precosKitMasculino['cheio']); ?> por
            R$<?php echo formatarPrecoHome($precosKitMasculino['promocional']); ?></p>
          <p class="fs-3 text-decoration-underline mt-1">Cupom: KITPIMA</p>
          <img class="w-100" src="<?php echo htmlspecialchars($kitMasculino['imagem'], ENT_QUOTES, 'UTF-8'); ?>"
            alt="<?php echo htmlspecialchars($kitMasculino['nome'], ENT_QUOTES, 'UTF-8'); ?>" />
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

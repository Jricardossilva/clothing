<?php
    require 'config/conexao.php';

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    $id = (int) $_GET['id'];
    $errors = [];

    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch();

    if (!$produto) {
        header("Location: index.php");
        exit;
    } 
    
    $imagemProduto = !empty($produto['url_imagem']) ? $produto['url_imagem'] : 'assets/img/camiseta-preta.jpg';
    $precoFormatado = number_format((float) $produto['preco'], 2, ',', '.');
    $dadosProdutoJs = json_encode([
        'id' => (string) $produto['id'],
        'name' => $produto['nome'],
        'price' => $precoFormatado,
        'image' => $imagemProduto,
        'url' => 'produto.php?id=' . (int) $produto['id'],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($produto['nome']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/index.css" />
  <link rel="stylesheet" href="assets/css/produto.css">
</head>

<body>
  <?php
  include("includes/header.php")
  ?>
  <div class="container py-5">
    <div class="row">

      <div class="col-md-6 d-flex justify-content-center">
        <div class="product-image-wrapper">
          <button
            class="favorite-btn"
            type="button"
            data-product-id="<?= (int) $produto['id'] ?>"
            aria-label="Favoritar produto">
            <span class="favorite-btn__icon">&#9825;</span>
          </button>

          <img src="<?= htmlspecialchars($imagemProduto)?>" alt="<?= htmlspecialchars($produto['nome']) ?>" class="img-fluid rounded d-block w-100">
        </div>
      </div>


      <div class="col-md-6">
        <h5 class="text-muted">⭐️⭐️⭐️⭐️⭐️ 84 Avaliações</h5>
        <h2 class="fw-bold"><?= htmlspecialchars($produto['nome'])?></h2>
        <p class="fs-4 fw-bold text-dark">R$ <?= htmlspecialchars($produto['preco' ])?></p>
        <?php $valorProduto = $produto['preco'];
          $valorDividido = $valorProduto / 3 ?>

        <p class="fs-4 text-red">ou até 3x de R$ <?php echo number_format($valorDividido, 2, ',', '.') ?> </p>
        

        <div class="mb-3">
          <label class="form-label fw-bold">Tamanho:</label><br>
          <div class="btn-group" role="group">
            <button class="btn btn-outline-secondary">PP</button>
            <button class="btn btn-outline-secondary">P</button>
            <button class="btn btn-outline-secondary">M</button>
            <button class="btn btn-outline-secondary">G</button>
            <button class="btn btn-outline-secondary">GG</button>
            <button class="btn btn-outline-secondary">XGG</button>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Cores disponíveis:</label><br>
          <span class="color-swatch color-black"></span>
          <span class="color-swatch color-navy"></span>
          <span class="color-swatch color-gray"></span>
          <span class="color-swatch color-blue"></span>
          <span class="color-swatch color-green"></span>
        </div>


        <div class="mb-3">
          <label class="form-label fw-bold">Quantidade:</label>
          <div class="input-group" style="width: 140px;">
            <button class="btn btn-outline-secondary" type="button" data-quantity-action="decrease">-</button>
            <input type="text" class="form-control text-center" value="1" data-product-quantity inputmode="numeric">
            <button class="btn btn-outline-secondary" type="button" data-quantity-action="increase">+</button>
          </div>
        </div>


        <div class="mb-3">
          <label class="form-label fw-bold">Calcular frete:</label>
          <input type="text" class="form-control" placeholder="Digite seu CEP" style="max-width: 200px;">
        </div>

        <?php
          $qtdEstoque = $produto['estoque'];
          if($qtdEstoque > 0){
            echo '<p class="text-success fw-bold">🟢 Item está em estoque</p>';
          }else{
            echo '<p class="text-danger fw-bold">🔴 Produto em falta</p>';
          }
        ?>
        


        <button
          class="btn btn-dark btn-lg mt-2"
          type="button"
          data-add-to-cart
          data-product-id="<?= (int) $produto['id'] ?>"
          data-product-name="<?= htmlspecialchars($produto['nome']) ?>"
          data-product-price="<?= htmlspecialchars($precoFormatado) ?>"
          data-product-image="<?= htmlspecialchars($imagemProduto) ?>"
          data-product-url="<?= htmlspecialchars('produto.php?id=' . (int) $produto['id']) ?>">
          Adicionar ao carrinho
        </button>
       <br> <br>
        
       <p class="texto-destaque"><?= htmlspecialchars($produto['descricao']) ?></p>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    if (typeof registerProductData === 'function') {
      registerProductData(<?= $dadosProdutoJs ?>);
    }
  </script>
  <script src="assets/js/produto.js"></script>
</body>

</html>

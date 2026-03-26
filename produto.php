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
    


?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Camiseta Pima Masculina</title>
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
            data-product-id="camiseta-masculina-preto"
            aria-label="Favoritar produto">
            <span class="favorite-btn__icon">&#9825;</span>
          </button>

          <img src="uploads/<?= htmlspecialchars($produto['url_imagem'])?>" alt="Camiseta Preta" class="img-fluid rounded d-block w-100">
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


        <p class="text-success fw-bold">🟢 Item está em estoque</p>


        <button
          class="btn btn-dark btn-lg mt-2"
          type="button"
          data-add-to-cart
          data-product-id="camiseta-masculina-preto"
          data-product-name="Camiseta Masculina - Preto"
          data-product-price="79,90"
          data-product-image="assets/img/camiseta-preta.jpg">
          Adicionar ao carrinho
        </button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/produto.js"></script>
</body>

</html>

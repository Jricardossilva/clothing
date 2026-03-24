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
  <?php
  include("includes/header.php");
  ?>
  <main>
    
    <!-- Carrossel incial -->
    <div id="carouselExampleInterval" class="carousel slide text-center mt-3 h-75" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active" data-bs-interval="2000">
          <img class="banner rounded-1" src="assets/img/banner-1.jpg" class="d-block w-100" alt="..." />
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
    <!-- Cards de roupas iniciais -->
    <section class="mx-auto section-roupas">
      <h2 class="py-3 mt-3">Peças a partir de R$ 41,90</h2>
      <div class="row row-cols-1 row-cols-md-4 g-4">
        <div class="col">
          <div class="card">
            <img src="uploads/feminino1.png" class="card-img-top" alt="Camiseta feminina" />
            <div class="card-body text-center">
              <h5 class="card-title"></h5>
              <p class="card-text">
                <strong>
                  Camiseta Algodão Premium Feminina | Everyday Collection -
                  Verde Oliva
                </strong>
              </p>
              <p class="d-flex justify-content-center gap-3">
                <strong>R$ 75,99</strong> <del>R$ 189,00</del>
              </p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card">
            <img src="uploads/masculino2.png" class="card-img-top" alt="Camiseta masculina" />
            <div class="card-body text-center">
              <h5 class="card-title"></h5>
              <p class="card-text">
                <strong>
                  Camiseta Algodão Premium Feminina | Everyday Collection -
                  Verde Oliva
                </strong>
              </p>
              <p class="d-flex justify-content-center gap-3">
                <strong>R$ 75,99</strong> <del>R$ 189,00</del>
              </p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card">
            <img src="uploads/feminino3.png" class="card-img-top" alt="Camiseta feminina" />
            <div class="card-body text-center">
              <h5 class="card-title"></h5>
              <p class="card-text">
                <strong>
                  Camiseta Algodão Premium Feminina | Everyday Collection -
                  Verde Oliva
                </strong>
              </p>
              <p class="d-flex justify-content-center gap-3">
                <strong>R$ 75,99</strong> <del>R$ 189,00</del>
              </p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card">
            <img src="uploads/masculino4.png" class="card-img-top" alt="Camiseta masculina" />
            <div class="card-body text-center">
              <h5 class="card-title"></h5>
              <p class="card-text">
                <strong>
                  Camiseta Algodão Premium Feminina | Everyday Collection -
                  Verde Oliva
                </strong>
              </p>
              <p class="d-flex justify-content-center gap-3">
                <strong>R$ 75,99</strong> <del>R$ 189,00</del>
              </p>
            </div>
          </div>
        </div>
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
        <div class="col">
          <div class="card">
            <img src="uploads/feminino5.png" class="card-img-top" alt="Camiseta feminina" />
            <div class="card-body text-center">
              <h5 class="card-title"></h5>
              <p class="card-text">
                <strong>
                  Camiseta Algodão Premium Feminina | Everyday Collection -
                  Verde Oliva
                </strong>
              </p>
              <p class="d-flex justify-content-center gap-3">
                <strong>R$ 75,99</strong> <del>R$ 189,00</del>
              </p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card">
            <img src="uploads/masculino6.png" class="card-img-top" alt="Camiseta masculina" />
            <div class="card-body text-center">
              <h5 class="card-title"></h5>
              <p class="card-text">
                <strong>
                  Camiseta Algodão Premium Feminina | Everyday Collection -
                  Verde Oliva
                </strong>
              </p>
              <p class="d-flex justify-content-center gap-3">
                <strong>R$ 75,99</strong> <del>R$ 189,00</del>
              </p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card">
            <img src="uploads/feminino7.png" class="card-img-top" alt="Camiseta feminina" />
            <div class="card-body text-center">
              <h5 class="card-title"></h5>
              <p class="card-text">
                <strong>
                  Camiseta Algodão Premium Feminina | Everyday Collection -
                  Verde Oliva
                </strong>
              </p>
              <p class="d-flex justify-content-center gap-3">
                <strong>R$ 75,99</strong> <del>R$ 189,00</del>
              </p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card">
            <img src="uploads/masculino8.png" class="card-img-top" alt="Camiseta masculina" />
            <div class="card-body text-center">
              <h5 class="card-title"></h5>
              <p class="card-text">
                <strong>
                  Camiseta Algodão Premium Feminina | Everyday Collection -
                  Verde Oliva
                </strong>
              </p>
              <p class="d-flex justify-content-center gap-3">
                <strong>R$ 75,99</strong> <del>R$ 189,00</del>
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <div class="text-center mt-4">
      <button>
        <span class="box">Ver tudo !</span>
      </button>
    </div>
    <!-- Área de Kits -->
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
          <p class="fs-2">de R$255 por R$195</p>
          <p class="fs-3 mt-1 text-decoration-underline">Cupom: KITPIMA</p>
          <img class="w-100" src="uploads/feminino9.png" alt="Kit feminino" />
          <a href="" class="my-3 fw-bold text-uppercase menu__link">Comprar</a>
        </div>
        <div class="d-flex flex-column align-items-center justify-content-center bg-white w-50">
          <h4 class="mt-5 mb-3 text-uppercase">Kit Camisetas</h4>
          <h3 class="fs-1 fw-bold">Prima Masculino</h3>
          <p class="fs-2">de R$255 por R$195</p>
          <p class="fs-3 text-decoration-underline mt-1">Cupom: KITPIMA</p>
          <img class="w-100" src="uploads/masculino1.png" alt="Kit masculino" />
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
        <div class="card w-25">
          <img src="uploads/feminino10.png" class="card-img-top" alt="Look feminino" />
          <div class="card-body">
            <p class="card-text text-center">
              <a href="#" class="fs-5 menu__link">Comprar</a>
            </p>
          </div>
        </div>
        <div class="card w-25">
          <img src="uploads/masculino2.png" class="card-img-top" alt="Look masculino" />
          <div class="card-body">
            <p class="card-text text-center">
              <a href="#" class="fs-5 menu__link">Comprar</a>
            </p>
          </div>
        </div>
        <div class="card w-25">
          <img src="uploads/masculino3.png" class="card-img-top" alt="Look masculino" />
          <div class="card-body">
            <p class="card-text text-center">
              <a href="#" class="fs-5 menu__link">Comprar</a>
            </p>
          </div>
        </div>
        <div class="card w-25">
          <img src="uploads/masculino4.png" class="card-img-top" alt="Look masculino" />
          <div class="card-body">
            <p class="card-text text-center">
              <a href="#" class="fs-5 menu__link">Comprar</a>
            </p>
          </div>
        </div>
      </div>
    </section>
  </main>
  <?php
  include("includes/footer.php");
  ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>

  <script src="assets/js/index.js"></script>
</body>

</html>

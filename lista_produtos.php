<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Produtos Masculinos</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/lista_produtos.css" />

</head>

<body>
    <?php
    include('includes/header.php');
    ?>
    <main>
        <h2 class="text-center mt-4 fw-bold">PRODUTOS MASCULINOS</h2>


        <div class="container d-flex mt-4">


            <aside class="sidebar">

                <h3>Ordenar por</h3>
                <label><input type="radio" name="ordenar"> Menor preço</label>
                <label><input type="radio" name="ordenar"> Maior preço</label>
                <label><input type="radio" name="ordenar"> Mais vendidos</label>
                <label><input type="radio" name="ordenar"> Lançamentos</label>

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

                <a href="produto.php" class="product-link">
                    <div class="product">
                        <img src="assets/img/camiseta-preta.jpg" alt="Camiseta Básica Preta" />
                        <div class="name">Camiseta Básica Preta</div>
                        <div class="price">R$ 29,90 <span class="old-price">R$ 39,90</span></div>
                    </div>
                </a>

                <a href="produtos.html" class="product-link">
                    <div class="product">
                        <img src="../images/camiseta-preta.jpg" alt="Camiseta Branca" />
                        <div class="name">Camiseta Branca</div>
                        <div class="price">R$ 29,90 <span class="old-price">R$ 39,90</span></div>
                    </div>
                </a>

                <a href="produtos.html" class="product-link">
                    <div class="product">
                        <img src="../images/camiseta-preta.jpg" alt="Camiseta Azul Marinho" />
                        <div class="name">Camiseta Azul Marinho</div>
                        <div class="price">R$ 29,90 <span class="old-price">R$ 39,90</span></div>
                    </div>
                </a>

                <a href="produtos.html" class="product-link">
                    <div class="product">
                        <img src="../images/camiseta-preta.jpg" alt="Camiseta Básica Preta" />
                        <div class="name">Camiseta Básica Preta</div>
                        <div class="price">R$ 29,90 <span class="old-price">R$ 39,90</span></div>
                    </div>
                </a>

                <a href="produtos.html" class="product-link">
                    <div class="product">
                        <img src="../images/camiseta-preta.jpg" alt="Camiseta Branca" />
                        <div class="name">Camiseta Branca</div>
                        <div class="price">R$ 29,90 <span class="old-price">R$ 39,90</span></div>
                    </div>
                </a>

                <a href="produtos.html" class="product-link">
                    <div class="product">
                        <img src="../images/camiseta-preta.jpg" alt="Camiseta Azul Marinho" />
                        <div class="name">Camiseta Azul Marinho</div>
                        <div class="price">R$ 29,90 <span class="old-price">R$ 39,90</span></div>
                    </div>
                </a>

                <a href="produtos.html" class="product-link">
                    <div class="product">
                        <img src="../images/camiseta-preta.jpg" alt="Camiseta Básica Preta" />
                        <div class="name">Camiseta Básica Preta</div>
                        <div class="price">R$ 29,90 <span class="old-price">R$ 39,90</span></div>
                    </div>
                </a>

                <a href="produtos.html" class="product-link">
                    <div class="product">
                        <img src="../images/camiseta-preta.jpg" alt="Camiseta Branca" />
                        <div class="name">Camiseta Branca</div>
                        <div class="price">R$ 29,90 <span class="old-price">R$ 39,90</span></div>
                    </div>
                </a>

                <a href="produtos.html" class="product-link">
                    <div class="product">
                        <img src="../images/camiseta-preta.jpg" alt="Camiseta Azul Marinho" />
                        <div class="name">Camiseta Azul Marinho</div>
                        <div class="price">R$ 29,90 <span class="old-price">R$ 39,90</span></div>
                    </div>
                </a>

            </section>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>


</body>

</html>
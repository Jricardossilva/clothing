    <header>
      <div class="d-flex justify-content-between align-items-center px-3 pt-2">
        <div class="d-flex justify-content-start gap-3 w-25">
          <a href=""><img class="icons" src="assets/img/instagram-icon.png" alt="" /></a>
          <a href=""><img class="icons" src="assets/img/tiktok-icon.png" alt="" /></a>
          <a href=""><img class="icons" src="assets/img/youtube-icon.png" alt="" /></a>
          <a href=""><img class="icons" src="assets/img/linkedin-icon.png" alt="" /></a>
          <a href=""><img class="icons" src="assets/img/facebook-icon.png" alt="" /></a>
        </div>

        <div class="d-flex justify-content-center w-50">
          <a href="index.php"><img
              class="logo-marca"
              src="assets/img/logo-marca.png"
              alt="Logo da Marca" /></a>
        </div>

        <div class="d-flex justify-content-end gap-3 w-25">

          <a href="#" class="favorites-header-link" data-bs-toggle="modal" data-bs-target='#modalFavoritos'>
            <img class="icons" src="assets/img/coracao-icon.png" alt="" />
            <span class="favorites-count-badge" data-favorites-count>0</span>
          </a>
          <!-- Modal -->
          <div class="modal fade" id="modalFavoritos" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Favoritos</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div data-favorites-list></div>
                </div>
              </div>
            </div>
          </div>

          <a href="#" class="favorites-header-link" data-bs-toggle="offcanvas" data-bs-target="#janelaCarrinho" aria-controls="janelaCarrinho">
            <img class="icons" src="assets/img/sacola-icon.png" alt="Carrinho" />
            <span class="favorites-count-badge" data-cart-count>0</span>
          </a>
          <!-- Offcanvas -->
          <div class="offcanvas offcanvas-end" tabindex="-1" id="janelaCarrinho" aria-labelledby="janelaCarrinhoLabel">
            <div class="offcanvas-header">
              <h5 id="janelaCarrinhoLabel">Carrinho</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body" data-cart-list>
              Aqui vão os produtos adicionados ao carrinho.
            </div>
            <div class="offcanvas-footer border-top p-3">
              <div class="header-cart-total mb-3">
                <span>Total</span>
                <strong data-cart-total>R$ 0,00</strong>
              </div>
              <a href="checkout.php" class="btn btn-dark w-100">Ir para pagamento</a>
            </div>
          </div>


          <a href="./login.php" target="blank"><img class="icons" src="assets/img/conta-icon.png" alt="" /></a>
        </div>
      </div>

      <!-- ✅ MENU ATUALIZADO COM DROPDOWN -->
      <nav class="d-flex gap-5 justify-content-center flex-wrap mt-5">
        <div class="menu-item">
          <a href="#" class="menu__link">Lançamento</a>
          <div class="submenu">
            <a href="#">Camisetas</a>
            <a href="#">Calças</a>
            <a href="#">Bermudas</a>
            <a href="#">Acessórios</a>
          </div>
        </div>
        <div class="menu-item">
          <a href="lista_produtos.php?genero=masculino" class="menu__link">Masculino</a>
          <div class="submenu">
            <a href="#">Camisetas</a>
            <a href="#">Calças</a>
            <a href="#">Bermudas</a>
            <a href="#">Acessórios</a>
          </div>
        </div>
        <div class="menu-item">
          <a href="lista_produtos.php?genero=feminino" class="menu__link">Feminino</a>
          <div class="submenu">
            <a href="#">Blusas</a>
            <a href="#">Vestidos</a>
            <a href="#">Saias</a>
            <a href="#">Moda Fitness</a>
          </div>
        </div>
        <div class="menu-item">
          <a href="#" class="menu__link">Destaques</a>
          <div class="submenu">
            <a href="#">Camisetas</a>
            <a href="#">Calças</a>
            <a href="#">Bermudas</a>
            <a href="#">Acessórios</a>
          </div>
        </div>
        <div class="menu-item">
          <a href="#" class="menu__link">Promoção</a>
          <div class="submenu">
            <a href="#">Camisetas</a>
            <a href="#">Calças</a>
            <a href="#">Bermudas</a>
            <a href="#">Acessórios</a>
          </div>
        </div>
      </nav>
    </header>
    <script src="assets/js/produtos.js"></script>
    <script src="assets/js/favoritos.js"></script>
    <script src="assets/js/carrinho.js"></script>

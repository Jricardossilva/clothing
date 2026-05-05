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

    function normalizarListaOpcoes(array $opcoes, bool $maiusculo = false): array
    {
        $opcoesTratadas = [];

        foreach ($opcoes as $opcao) {
            $valor = trim((string) $opcao);

            if ($valor === '') {
                continue;
            }

            $opcoesTratadas[] = $maiusculo ? strtoupper($valor) : $valor;
        }

        return array_values(array_unique($opcoesTratadas));
    }

    function normalizarCaminhoImagemProduto(?string $caminho): string
    {
        $caminho = trim((string) $caminho);

        if ($caminho === '') {
            return 'assets/img/camiseta-preta.jpg';
        }

        if (
            str_starts_with($caminho, 'http://') ||
            str_starts_with($caminho, 'https://') ||
            str_starts_with($caminho, 'uploads/') ||
            str_starts_with($caminho, 'assets/')
        ) {
            return $caminho;
        }

        return 'uploads/' . ltrim($caminho, '/');
    }

    function obterCorCss(string $cor): string
    {
        $corTratada = trim($cor);

        if (preg_match('/^#(?:[0-9a-f]{3}){1,2}$/i', $corTratada)) {
            return $corTratada;
        }

        $mapaCores = [
            'amarelo' => '#ffff00',
            'azul' => '#3399ff',
            'bege' => '#f5f5dc',
            'branco' => '#ffffff',
            'cinza' => '#808080',
            'laranja' => '#ffa500',
            'marrom' => '#8b4513',
            'preto' => '#000000',
            'rosa' => '#ffc0cb',
            'roxo' => '#800080',
            'verde' => '#2e8b57',
            'vermelho' => '#ff0000',
        ];

        return $mapaCores[strtolower($corTratada)] ?? $corTratada;
    }

    $tamanhosDisponiveis = normalizarListaOpcoes([$produto['tamanho'] ?? ''], true);
    $coresDisponiveis = normalizarListaOpcoes([$produto['cor'] ?? '']);

    $stmtTabelaVariacoes = $pdo->query("SHOW TABLES LIKE 'produto_variacoes'");
    $tabelaVariacoesExiste = (bool) $stmtTabelaVariacoes->fetchColumn();

    if ($tabelaVariacoesExiste) {
        $stmtVariacoes = $pdo->prepare(
            'SELECT cor, tamanho
             FROM produto_variacoes
             WHERE produto_id = ?
               AND COALESCE(estoque, 0) > 0'
        );
        $stmtVariacoes->execute([$id]);
        $variacoes = $stmtVariacoes->fetchAll();

        $tamanhosVariacoes = normalizarListaOpcoes(array_column($variacoes, 'tamanho'), true);
        $coresVariacoes = normalizarListaOpcoes(array_column($variacoes, 'cor'));

        if ($tamanhosVariacoes) {
            $tamanhosDisponiveis = $tamanhosVariacoes;
        }

        if ($coresVariacoes) {
            $coresDisponiveis = $coresVariacoes;
        }
    }
    
    $imagemProduto = normalizarCaminhoImagemProduto($produto['url_imagem'] ?? null);
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
    <div id="alertGlobal" class="mb-3"></div>
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
        <h2 class="fw-bold product-name-text"><?= htmlspecialchars($produto['nome'])?></h2>
        <p class="fs-4 fw-bold product-price-text">R$ <?= htmlspecialchars($produto['preco' ])?></p>
        <?php $valorProduto = $produto['preco'];
          $valorDividido = $valorProduto / 3 ?>

        <p class="fs-4 product-installments">ou até 3x de R$ <?php echo number_format($valorDividido, 2, ',', '.') ?> </p>
        

        <div class="mb-3">
          <label class="form-label fw-bold">Tamanho:</label><br>
          <?php if ($tamanhosDisponiveis): ?>
            <div class="btn-group" role="group" aria-label="Tamanhos disponiveis">
              <?php foreach ($tamanhosDisponiveis as $indice => $tamanhoDisponivel): ?>
                <input
                  type="radio"
                  class="btn-check"
                  name="tamanho"
                  id="tamanho-<?= $indice ?>"
                  value="<?= htmlspecialchars($tamanhoDisponivel, ENT_QUOTES, 'UTF-8') ?>"
                  <?= $indice === 0 ? 'checked' : '' ?>>
                <label class="btn btn-outline-secondary" for="tamanho-<?= $indice ?>">
                  <?= htmlspecialchars($tamanhoDisponivel, ENT_QUOTES, 'UTF-8') ?>
                </label>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted mb-0">Nenhum tamanho cadastrado.</p>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Cor disponível:</label><br>
          <?php if ($coresDisponiveis): ?>
            <div class="product-color-options" role="group" aria-label="Cores disponiveis">
              <?php foreach ($coresDisponiveis as $indice => $corDisponivel): ?>
                <input
                  type="radio"
                  class="color-radio"
                  name="cor"
                  id="cor-<?= $indice ?>"
                  value="<?= htmlspecialchars($corDisponivel, ENT_QUOTES, 'UTF-8') ?>"
                  <?= $indice === 0 ? 'checked' : '' ?>>
                <label
                  class="color-swatch"
                  for="cor-<?= $indice ?>"
                  title="<?= htmlspecialchars($corDisponivel, ENT_QUOTES, 'UTF-8') ?>"
                  style="background-color: <?= htmlspecialchars(obterCorCss($corDisponivel), ENT_QUOTES, 'UTF-8') ?>;">
                  <span class="visually-hidden"><?= htmlspecialchars($corDisponivel, ENT_QUOTES, 'UTF-8') ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted mb-0">Nenhuma cor cadastrada.</p>
          <?php endif; ?>
        </div>


        <div class="mb-3">
          <label class="form-label fw-bold">Quantidade:</label>
          <div class="input-group" style="width: 140px;">
            <button class="btn btn-outline-secondary" type="button" data-quantity-action="decrease">-</button>
            <input type="text" class="form-control text-center" value="1" data-product-quantity inputmode="numeric">
            <button class="btn btn-outline-secondary" type="button" data-quantity-action="increase">+</button>
          </div>
        </div>
       
        <?php
          $qtdEstoque = $produto['estoque'];
          if ($qtdEstoque > 0) {
            // Note o uso de pontos '.' para juntar o texto com as variáveis PHP
            echo '<button
                    class="btn btn-dark btn-lg mt-2"
                    type="button"
                    data-add-to-cart
                    data-product-id="' . (int)$produto['id'] . '"
                    data-product-name="' . htmlspecialchars($produto['nome']) . '"
                    data-product-price="' . htmlspecialchars($precoFormatado) . '"
                    data-product-image="' . htmlspecialchars($imagemProduto) . '"
                    data-product-url="' . htmlspecialchars('produto.php?id=' . (int)$produto['id']) . '">
                    Adicionar ao carrinho
                  </button>';
            
            echo '<p class="text-success fw-bold">🟢 Item está em estoque</p>';
        }        
           else {
              echo '<p class="text-danger fw-bold">🔴 Produto em falta</p>';
              echo '<div class="alert alert-secondary mt-3">
                      <!-- ADICIONADO: ID mensagemAlerta -->
                      <div id="mensagemAlerta"></div> 
                      
                      <p class="small mb-2"> Quer ser avisado quando chegar? </p>
                      
                      <!-- ADICIONADO: ID meuFormAvisar -->
                      <form id="meuFormAvisar"  method="POST" class="d-flex gap-2">
                        <input type="hidden" name="produto_id" value="' . (int)$produto['id'] . '">
                        
                        <!-- ADICIONADO: ID emailCliente -->
                        <input type="email" id="emailCliente" name="email" class="form-control form-control-sm" placeholder="Seu e-mail" required>
                        
                        <button type="submit" class="col-auto btn btn-sm btn-dark">Avisar-me</button>
                      </form>
                    </div>';
          }
          ?>               
       <br> <br>
        
       <p class="texto-destaque"><?= htmlspecialchars($produto['descricao']) ?></p>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    if (typeof registerProductData === 'function') {
      registerProductData(<?= $dadosProdutoJs ?>);
    }
  </script>
  <script src="assets/js/produto.js"></script>

<script>
  document.querySelectorAll('[data-add-to-cart]').forEach(button => {
    button.addEventListener('click', function() {

      const nome = this.getAttribute('data-product-name');

      Swal.fire({
        position: "top-end",
        icon: "success",
        title: `Produto adicionado ao carrinho 🛒`,
        showConfirmButton: false,
        timer: 1500
      });

    });
  });
</script>

  <script>
  const form = document.getElementById('meuFormAvisar');
  if (form) {
    form.addEventListener('submit', function(e) {
      // Impede o recarregamento imediato para mostrar o alerta
      e.preventDefault(); 
      
      const email = document.getElementById('emailCliente').value;
      const placeholder = document.getElementById('mensagemAlerta');
      
      // Mostra o alerta
      placeholder.innerHTML = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          E-mail <strong>${email}</strong> cadastrado com sucesso!
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;

      // Opcional: Envia o formulário de verdade após 2 segundos
      setTimeout(() => {
        this.submit();
      }, 2000);
    });
  }
</script>
 <?php include 'includes/footer.php'; ?>
</body>

</html>

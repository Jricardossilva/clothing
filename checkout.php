<?php

session_start();
require 'config/conexao.php';

$valorFrete = 0;

// =============================
// PROCESSAMENTO DA COMPRA
// =============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'pagar') {

    $carrinho = json_decode($_POST['carrinho'] ?? '', true);
    try {
        if (!is_array($carrinho)) {
            throw new Exception("Carrinho invalido");
        }

        $itensCarrinho = [];

        foreach ($carrinho as $chave => $item) {
            if (is_array($item)) {
                $produto_id = (int) ($item['id'] ?? 0);
                $quantidade = (int) ($item['quantity'] ?? 0);
            } else {
                $produto_id = (int) $chave;
                $quantidade = (int) $item;
            }

            if ($produto_id > 0 && $quantidade > 0) {
                $itensCarrinho[$produto_id] = ($itensCarrinho[$produto_id] ?? 0) + $quantidade;
            }
        }

        if (empty($itensCarrinho)) {
            throw new Exception("Carrinho vazio");
        }

        $pdo->beginTransaction();
        // =========================
        // DADOS CLIENTE
        // =========================
        $nome = $_POST['nome'];
        $sobrenome = $_POST['sobrenome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];

        // =========================
        // DADOS PAGAMENTO / FRETE
        // =========================
        $metodosPagamento = [
            'credit' => 'credito',
            'credito' => 'credito',
            'debit' => 'debito',
            'debito' => 'debito',
            'pix' => 'pix',
            'boleto' => 'boleto',
        ];
        $metodo_pagamento = $metodosPagamento[$_POST['paymentMethod'] ?? ''] ?? '';

        if ($metodo_pagamento === '') {
            throw new Exception("Metodo de pagamento invalido");
        }
        $frete = floatval($_POST['frete'] ?? 0);

        // =========================
        // DADOS ENDEREÇO
        // =========================
        $logradouro = $_POST['rua'];
        $numero = $_POST['numero'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];
        $cep = $_POST['cep'];
        $pais = $_POST['pais'];

        $total = 0;

            // =========================
            // 1. CRIAR CLIENTE
            // =========================
            $stmt = $pdo->prepare("
                INSERT INTO clientes (nome, sobrenome, email, telefone)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$nome, $sobrenome, $email, $telefone]);

            $cliente_id = $pdo->lastInsertId();

            // =========================
            // 2. INSERIR ENDEREÇO
            // =========================
            $stmt = $pdo->prepare("
                INSERT INTO endereco 
                (cliente_id, logradouro, numero, bairro, cidade, estado, cep, pais)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $cliente_id,
                $logradouro,
                $numero,
                $bairro,
                $cidade,
                $estado,
                $cep,
                $pais
            ]);

            $endereco_id = $pdo->lastInsertId();

            // =========================
            // 3. CALCULAR TOTAL + LOCK ESTOQUE
            // =========================
            foreach ($itensCarrinho as $produto_id => $quantidade) {

            $stmt = $pdo->prepare("
                SELECT id, estoque, preco 
                FROM produtos
                WHERE id = ?
                FOR UPDATE
            ");
            $stmt->execute([$produto_id]);

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) {
                throw new Exception("Produto não encontrado");
            }

            if ($dados['estoque'] < $quantidade) {
                throw new Exception("Estoque insuficiente");
            }

            $total += $dados['preco'] * $quantidade;
        }

        $total += $frete;

        // =========================
        // 4. CRIAR PEDIDO
        // =========================
        $stmt = $pdo->prepare("
            INSERT INTO tabela_pedidos
            (cliente_id, endereco_entrega_id, valor_total, valor_frete, status_compra)
            VALUES (?, ?, ?, ?, 'AGUARDANDO PAGAMENTO')
        ");
        $stmt->execute([$cliente_id, $endereco_id, $total, $frete]);

        $pedido_id = $pdo->lastInsertId();

        // =========================
        // 5. PAGAMENTO
        // =========================
        $codigo_transacao = uniqid();

        $stmt = $pdo->prepare("
            INSERT INTO pagamento
            (pedido_id, metodo_pagamento, status_pagamento, codigo_transacao)
            VALUES (?, ?, 'aguardando', ?)
        ");
        $stmt->execute([$pedido_id, $metodo_pagamento, $codigo_transacao]);

            // =========================
            // 6. ITENS + UPDATE ESTOQUE
            // =========================
            foreach ($itensCarrinho as $produto_id => $quantidade) {

                $stmt = $pdo->prepare("
                    SELECT preco 
                    FROM produtos
                    WHERE id = ?
                ");
                $stmt->execute([$produto_id]);

                $dados = $stmt->fetch(PDO::FETCH_ASSOC);

                $preco = $dados['preco'];
                $subtotal = $preco * $quantidade;

                // INSERT ITEM
                $stmt = $pdo->prepare("
                    INSERT INTO itens_pedido
                    (pedido_id, produto_id, quantidade, preco_unitario, subtotal)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $pedido_id,
                    $produto_id,
                    $quantidade,
                    $preco,
                    $subtotal
                ]);

                // UPDATE ESTOQUE
                $stmt = $pdo->prepare("
                    UPDATE produtos
                    SET estoque = estoque - ?
                    WHERE id = ?
                ");
                $stmt->execute([$quantidade, $produto_id]);
            }

            // =========================
            // FINALIZAR
            // =========================
            $pdo->commit();


            echo "<script>
                localStorage.removeItem('shoppingCart');
                localStorage.removeItem('checkoutCouponCode');
                window.location.href = 'index.php';
            </script>";

        } catch (Exception $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollback();
            }

            echo "<script>alert('Erro: " . $e->getMessage() . "');</script>";
        }
}

// =============================
// FRETE
// =============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'frete') {
    $estado = $_POST['estado'] ?? '';
    $peso = $_POST['peso'] ?? 1;

    if (!$estado || $peso <= 0) {
        $resultado = ['erro' => "Dados inválidos."];
    } else {
        $resultado = calcularFreteSimulado($estado, $peso);
        $valorFrete = $resultado['valor_numerico'] ?? 0;
    }
}

function getDistanciasSP()
{
    return [
        'SP' => 50,
        'RJ' => 430,
        'MG' => 500,
        'ES' => 850,
        'PR' => 400,
        'SC' => 700,
        'RS' => 1100,
        'BA' => 1500,
        'PE' => 2100,
        'CE' => 2600,
        'PB' => 2800,
        'RN' => 2900,
        'GO' => 900,
        'DF' => 1000,
        'MT' => 1400,
        'MS' => 1000,
        'AM' => 3900,
        'PA' => 3000,
        'MA' => 2600,
        'PI' => 2400,
        'AL' => 2200,
        'SE' => 2000,
        'RO' => 2800,
        'AC' => 3500,
        'AP' => 3300,
        'RR' => 4500,
        'TO' => 1800,
    ];
}

function calcularFreteSimulado($estadoDestino, $peso)
{
    $distancias = getDistanciasSP();

    $distancia = $distancias[$estadoDestino] ?? null;

    $valorBase = 10.00;
    $custoPorKm = 0.02;
    $custoPorKg = 5.00;

    $valor = $valorBase + ($distancia * $custoPorKm) + ($peso * $custoPorKg);

    $prazo = ceil($distancia / 500);

    return [
        'estado' => $estadoDestino,
        'valor_numerico' => $valor,
        'valor' => number_format($valor, 2, ',', '.'),
        'prazo' => $prazo
    ];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/checkout.css">
    <link rel="stylesheet" href="https://cloudflare.com">

</head>

<body class="checkout-page">
    <div class="container py-4">
        <?php include 'includes/header.php'; ?>
        <hr>
        <h1 class="h2 mt-5">Formulário de pagamento</h1>
        <p class="lead"></p>

        <div>
            <div class="row g-5">

                <div class="col-md-5 col-lg-4 order-md-last">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-dark">Seu carrinho</span>
                        <span class="badge bg-dark rounded-pill" id="checkoutCartCount">0</span>
                    </h4>
                    <ul class="list-group mb-3" id="checkoutCartItems">
                        <li class="list-group-item text-center text-body-secondary">
                            Seu carrinho esta vazio.
                        </li>
                    </ul>
	                    <ul class="list-group mb-3">
	                        <li class="list-group-item d-flex justify-content-between">
	                            <span>Frete</span>
	                            <strong id="checkoutShippingCost" data-shipping-cost="<?php echo htmlspecialchars((string) $valorFrete, ENT_QUOTES, 'UTF-8'); ?>">
	                                <?php echo 'R$ ' . number_format($valorFrete, 2, ',', '.'); ?>
	                            </strong>
	                        </li>
	                        <li class="list-group-item d-flex justify-content-between text-success" id="checkoutDiscountRow" style="display: none;">
	                            <span>Desconto</span>
	                            <strong id="checkoutDiscountValue">- R$ 0,00</strong>
	                        </li>
	                        <li class="list-group-item d-flex justify-content-between">
	                            <span>Total (R$)</span>
	                            <strong id="checkoutCartTotal">R$ 0,00</strong>
	                        </li>
	                    </ul>
                    <div class="input-group">
                        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'frete'): ?>

                            <?php if (isset($resultado['erro'])): ?>
                                <p><?php echo $resultado['erro']; ?></p>
                            <?php else: ?>
                                <ul>
                                    <li><strong>Estado:</strong> <?php echo $resultado['estado']; ?></li>
                                    <li><strong>Tipo de envio:</strong> PAC</li>
                                    <li><strong>Valor:</strong> R$ <?php echo $resultado['valor']; ?></li>
                                    <li><strong>Prazo:</strong> <?php echo $resultado['prazo']; ?> dias úteis</li>
                                </ul>
                            <?php endif; ?>

                        <?php endif; ?>
                    </div>

	                    <form class="card p-2" id="checkoutCouponForm">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Código de desconto">
                            <button type="submit" class="btn btn-secondary">Resgatar</button>
                        </div>
                    </form>
                </div>


                <div class="col-md-7 col-lg-8">
	                    <form method="POST" class="needs-validation mb-5" id="checkoutForm" novalidate>
                        <input type="hidden" name="carrinho" id="carrinhoInput">
                        <input type="hidden" name="frete" id="freteInput" value="<?php echo htmlspecialchars((string) $valorFrete, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="firstName" class="form-label">Nome</label>
                                <input type="text" class="form-control" name="nome" id="firstName" placeholder="" value="<?php echo $_POST['nome'] ?? '' ?> "style="text-transform: capitalize;" required>
                                <div class="invalid-feedback">
                                É necessário um nome válido.
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="lastName" class="form-label">Sobrenome</label>
                                <input type="text" class="form-control" name="sobrenome" id="lastName" placeholder="" value="<?php echo $_POST['sobrenome'] ?? '' ?>" style="text-transform: capitalize;" required>
                                <div class="invalid-feedback">
                                É necessário um sobrenome válido.
                                </div>
                            </div>
	                            <div class="col-12">
	                                <label for="email" class="form-label">Email</label>
	                                <input type="email" class="form-control" name="email" id="email" placeholder="nome@exemplo.com" value="<?php echo $_POST['email'] ?? '' ?>"
	                                    required>
	                                <div class="invalid-feedback">
	                                Por favor, insira um endereço de e-mail válido para receber atualizações sobre o envio.
	                                </div>
	                            </div>
	                            <div class="col-12">
	                                <label for="telefone" class="form-label">Numero de contato</label>
	                                <input type="text" class="form-control" name="telefone" id="telefone" placeholder="(00) 00000-0000" value="<?php echo $_POST['telefone'] ?? '' ?>" required>
	                                <div class="invalid-feedback">
	                                    Informe um numero de contato.
	                                </div>
	                            </div>
	
	                            <div class="col-4"> <label for="address" class="form-label">CEP</label>
                                <input type="text"
                                    class="form-control" name="cep" id="cep" placeholder="CEP" onblur="buscaCEP()" value="<?php echo $_POST['cep'] ?? '' ?>"  required>
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="col-8">
                                <label for="address" class="form-label">Endereço</label>
                                <input type="text"
                                    class="form-control" name="rua" id="logradouro" placeholder="Rua" value="<?php echo $_POST['rua'] ?? '' ?>" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input type="text"
                                    class="form-control" name="complemento" id="complemento" placeholder="Apartamento, bloco, casa..." value="<?php echo $_POST['complemento'] ?? '' ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="numero" class="form-label">Número</label>
                                <input type="text"
                                    class="form-control" name="numero" id="numero" placeholder="Número" value="<?php echo $_POST['numero'] ?? '' ?>" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-5"> <label for="address" class="form-label">Bairro</label>
                                <input type="text"
                                    class="form-control" name="bairro" id="bairro" placeholder="Bairro" value="<?php echo $_POST['bairro'] ?? '' ?>" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-4"> <label for="address" class="form-label">Cidade</label>
                                <input type="text"
                                    class="form-control" name="cidade" id="localidade" placeholder="Cidade" value="<?php echo $_POST['cidade'] ?? '' ?>" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-3"> <label for="address" class="form-label">Estado</label>
                                <input type="text"
                                    class="form-control" name="estado" id="uf" placeholder="Estado" value="<?php echo $_POST['estado'] ?? '' ?>" required>
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="country" class="form-label">País</label>
                                <select class="form-select" name="pais" id="country" required>
                                    <!-- A opção padrão deve ter value vazio para o 'required' funcionar -->
                                    <option value="" <?php echo !isset($_POST['pais']) ? 'selected' : ''; ?> disabled>Selecionar</option>
                                    
                                    <option value="United States" <?php echo (($_POST['pais'] ?? '') == 'United States') ? 'selected' : ''; ?>>EUA</option>
                                    
                                    <option value="Brasil" <?php echo (($_POST['pais'] ?? '') == 'Brasil') ? 'selected' : ''; ?>>Brasil</option>
                                </select>
                                <div class="invalid-feedback">
                                Por favor, selecione um país válido.
                                </div>
                            </div>


                            <button class="w-100 btn btn-primary btn-lg" type="submit" name="acao" value="frete">Calcular frete</button>
                           
                            <hr class="my-4">
                            <h4 class="mb-3">Pagamento</h4>
                            <div class="my-3">                            
                                <div class="form-check">
                                    <input id="credit" name="paymentMethod" type="radio" class="form-check-input" value="credit" required>
                                    <label class="form-check-label" for="credit">Cartão de crédito</label>
                                </div>

                                <div class="form-check">
                                    <input id="debit" name="paymentMethod" type="radio" class="form-check-input" value="debit" required>
                                    <label class="form-check-label" for="debit">Cartão de débito</label>
                                </div>

                                <div class="form-check">
                                    <input id="pix-Radio" name="paymentMethod" type="radio" class="form-check-input" value="pix" required>
                                    <label class="form-check-label" for="pix">Pix</label>
                                </div>
                            </div>
                            
                            <div class="row gy-3" id="cardPaymentFields" style="display: none;">
                            
                                <div class="col-md-6">
                                    <label for="cc-name" class="form-label">Nome do titular</label>
                                    <input type="text" class="form-control" id="cc-name" placeholder="">
                                    <small class="text-body-secondary"></small>
                                    <div class="invalid-feedback">
                                    É necessário preencher o cartão com o nome.
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                <label for="cpf" class="form-label">Numero do CPF</label>
                                <input type="text" class="form-control" id="cpf" placeholder="000.000.000-00" maxlength="14">
                                    <div class="invalid-feedback">
                                    É necessário um CPF válido.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="cc-number" class="form-label">Número do cartão</label>
                                    <input type="text"  class="form-control" id="cc-number" placeholder="0000 0000 0000 0000">
                                    <div class="invalid-feedback">
                                    É necessário o número do cartão de crédito.
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="cc-expiration" class="form-label">Data Validade</label>
                                    <input type="text" class="form-control" id="cc-expiration" placeholder="dd/mm">
                                    <div class="invalid-feedback">
                                    Data de validade necessária
                                    </div>
                                </div>                                
                                <div class="col-md-3">
                                    <label for="cc-cvv" class="form-label">CVV</label>
                                    <div class="input-group">
                                        <!-- Campo de Input (Borda esquerda arredondada pelo Bootstrap) -->
                                        <input type="password" class="form-control" id="cc-cvv" placeholder=" ">
                                        
                                        <!-- Botão com borda direita arredondada explicitamente -->
                                        <button class="btn btn-outline-secondary rounded-end" type="button" id="btn-toggle-cvv">
                                            <span id="eye-icon-container">
                                                <svg xmlns="http://w3.org" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                </svg>
                                            </span>
                                        </button>

                                        <div class="invalid-feedback">
                                            Security code required
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <hr class="my-4">
	                            <button class="w-100 btn btn-primary btn-lg mt-3" name="acao" value="pagar" id="checkoutSubmitPayment" type="submit">
	                                Realizar pagamento
	                            </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="checkout-page-footer">
        <?php include 'includes/footer.php'; ?>
    </div>

    <div id="modalPixContainer" class="modal">
        <div class="modal-dialog d-flex justify-content-center align-items-center">
            <div class="modal-content text-center p-4">
                <span class="close-btn" onclick="fecharModal()" style="position: absolute; right: 20px; top: 15px; font-size: 28px; cursor: pointer;">&times;</span>

                <h2 class="modal-title mb-3">Pagamento PIX</h2>

                <div class="qr-container mb-3">
                    <img src="../clothing/assets/img/testeqrcode.jpg" alt="QR Code PIX"
                        style="width: 100%; max-width: 250px; height: auto; margin: 0 auto; display: block; border: 1px solid #eee; padding: 15px; background: #fff;">
                    <p class="qr-instruction mt-2 mb-3" style="font-size: 15px; color: #666;">Aponte a cÃ¢mera do seu banco para o cÃ³digo acima</p>
                </div>

                <div class="upload-section d-flex flex-column align-items-center">
                    <label for="comprovante" class="mb-2 fw-bold">Anexar Comprovante:</label>
                    <input type="file" id="comprovante" class="form-control mb-3" style="max-width: 400px; width: 100%;" accept="image/*,.pdf">
                    <button type="button" class="btn btn-primary" onclick="enviarDados()" style="max-width: 400px; width: 100%; padding: 12px 0; font-size: 1.1rem;">
                        Confirmar Pagamento
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mascaraCEP(valor) {
            valor = valor.replace(/\D/g, "");
            valor = valor.substring(0, 8);
            valor = valor.replace(/(\d{5})(\d)/, "$1-$2");
            return valor;
        }

        async function buscaCEP() {
            const cep = document.getElementById('cep').value.replace(/\D/g, '');

            if (cep.length !== 8) return;

            try {
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();

                if (data.erro) {
                    alert("CEP não encontrado!");
                    return;
                }

                document.getElementById('logradouro').value = data.logradouro || '';
                document.getElementById('bairro').value = data.bairro || '';
                document.getElementById('localidade').value = data.localidade || '';
                document.getElementById('uf').value = data.uf || '';

            } catch (error) {
                console.error("Erro ao buscar o CEP:", error);
            }
        }

        // apenas máscara (SEM busca automática)
        document.getElementById('cep').addEventListener('input', function(e) {
            e.target.value = mascaraCEP(e.target.value);
        });

        // busca só no Enter
        document.getElementById('cep').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                buscaCEP();
            }
        });
    </script>

    <script>
        function mascaraTelefone(valor) {
            valor = valor.replace(/\D/g, "").substring(0, 11);; // remove tudo que não for número
            valor = valor.replace(/^(\d{2})(\d)/g, "($1) $2"); // (11) 9

            if (valor.length <= 13) {
                // telefone fixo: (11) 1234-5678
                valor = valor.replace(/(\d{4})(\d{4})$/, "$1-$2");
            } else {
                // celular: (11) 91234-5678
                valor = valor.replace(/(\d{5})(\d{4})$/, "$1-$2");
            }

            return valor;
        }

        document.getElementById("telefone").addEventListener("input", function(e) {
        e.target.value = mascaraTelefone(e.target.value);
        });
    </script>

    <script>
        function mascaraCartao(valor) {
        valor = valor.replace(/\D/g, ""); // remove tudo que não for número
        valor = valor.substring(0, 16);   // limita a 16 dígitos

        // adiciona espaço a cada 4 dígitos
        valor = valor.replace(/(\d{4})(?=\d)/g, "$1 ");

        return valor;
        }

        document.getElementById("cc-number").addEventListener("input", function(e) {
        e.target.value = mascaraCartao(e.target.value);
        });
    </script>
    <script>
        function mascaraValidade(valor) {
            valor = valor.replace(/\D/g, ""); // só números
            valor = valor.substring(0, 4);    // limita a 4 dígitos

            // adiciona a barra
            valor = valor.replace(/(\d{2})(\d)/, "$1/$2");

            return valor;
        }

        document.getElementById("cc-expiration").addEventListener("input", function(e) {
            e.target.value = mascaraValidade(e.target.value);
        });
    </script>
        <script>
        // Desenhos dos ícones (Aberto e Fechado)
        const iconOpen = `<svg xmlns="http://w3.org" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>`;
        const iconClosed = `<svg xmlns="http://w3.org" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.502 3.502 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/></svg>`;

        const cvvInput = document.getElementById('cc-cvv');
        const eyeContainer = document.getElementById('eye-icon-container');

        // Alternar visibilidade
        document.getElementById('btn-toggle-cvv').addEventListener('click', function () {
            if (cvvInput.type === 'password') {
                cvvInput.type = 'text';
                eyeContainer.innerHTML = iconClosed;
            } else {
                cvvInput.type = 'password';
                eyeContainer.innerHTML = iconOpen;
            }
        });

        // Máscara (só números)
        cvvInput.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/\D/g, "").substring(0, 3);
        });
        
    </script>

    <script>
        const inputCpf = document.getElementById('cpf');

        inputCpf.addEventListener('input', (e) => {
            let value = e.target.value;
            
            // Remove tudo o que não é dígito
            value = value.replace(/\D/g, "");
            
            // Aplica a máscara progressivamente
            value = value.replace(/(\d{3})(\d)/, "$1.$2");
            value = value.replace(/(\d{3})(\d)/, "$1.$2");
            value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
            
            e.target.value = value;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/checkout.js"></script>
             
    </body>
</html>

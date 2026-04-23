<?php

session_start();
require './config/conexao.php';

$valorFrete = 0;

// =============================
// PROCESSAMENTO DA COMPRA
// =============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_compra'])) {
    die('teste');
    $carrinho = isset($_POST['carrinho']) 
    ? json_decode($_POST['carrinho'], true) 
    : [];

    // DADOS CLIENTE
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];
    $email = $_POST['email'];
    
    // DADOS PAGAMENTO
    $metodo_pagamento = $_POST['metodo_pagamento'];
    $frete = floatval($_POST['frete'] ?? 0);

    // DADOS ENDEREÇO
    $logradouro = $_POST['rua'];
    $numero = $_POST['numero'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $cep = $_POST['cep'];
    $pais = $_POST['pais'];

    $total = 0;

    // $conn->begin_transaction();

    try {
        // =========================
        // 0. INSERIR CLIENTE
        // =========================
         $sql = "INSERT INTO cliente 
                (nome, sobrenome, email)
                VALUES 
                ('$nome', '$sobrenome', '$email')";

        if (!$conn->query($sql)) {
            throw new Exception("Erro ao salvar cliente");
        }
        echo 'teste2';
        $cliente_id = $conn->insert_id;
        echo 'teste3';

        // =========================
        // 1. INSERIR ENDEREÇO
        // =========================
        $sql = "INSERT INTO endereco 
                (cliente_id, logradouro, numero, bairro, cidade, estado, cep, pais)
                VALUES 
                ($cliente_id, '$logradouro', $numero, '$bairro', '$cidade', '$estado', '$cep', '$pais')";

        if (!$conn->query($sql)) {
            throw new Exception("Erro ao salvar endereço");
        }
        echo 'teste4';
        $endereco_id = $conn->insert_id;
        echo 'teste5';
        // =========================
        // 2. CALCULAR TOTAL
        // =========================

        foreach ($carrinho as $item) {
            $variacao_id = (int)$item['id'];
            $quantidade = (int)$item['quantity'];

            $sql = "SELECT pv.produto_id, p.preco 
                    FROM produto_variacoes pv
                    INNER JOIN produtos p ON p.id = pv.produto_id
                    WHERE pv.id = $variacao_id";

            $result = $conn->query($sql);
            $dados = $result->fetch_assoc();
            die($dados);

            $produto_id = $dados['produto_id'];
            $preco = $dados['preco'];
            $subtotal = $preco * $quantidade;
            
            if (!$dados) {
                throw new Exception("Produto não encontrado");
            }

            if ($dados['estoque'] < $quantidade) {
                throw new Exception("Estoque insuficiente");
            }

            $total += $subtotal;
        }

        $total += $frete;

        // =========================
        // 3. INSERT PEDIDO
        // =========================
        $sql = "INSERT INTO tabela_pedidos
                (cliente_id, endereco_entrega_id, valor_total, valor_frete, status_compra)
                VALUES
                ($cliente_id, $endereco_id, $total, $frete, 'AGUARDANDO PAGAMENTO')";

        if (!$conn->query($sql)) {
            throw new Exception("Erro ao criar pedido");
        }

        $pedido_id = $conn->insert_id;

        // =========================
        // 4. INSERT PAGAMENTO
        // =========================
        $codigo_transacao = uniqid();

        $sql = "INSERT INTO pagamento
                (pedido_id, metodo_pagamento, status_pagamento, codigo_transacao)
                VALUES
                ($pedido_id, '$metodo_pagamento', 'aguardando', '$codigo_transacao')";

        if (!$conn->query($sql)) {
            throw new Exception("Erro no pagamento");
        }

        // =========================
        // 5. ITENS + ESTOQUE
        // =========================
        foreach ($carrinho as $item) {
            $variacao_id = (int)$item['id'];
            $quantidade = (int)$item['quantity'];

            $sql = "SELECT pv.produto_id, p.preco 
                    FROM produto_variacoes pv
                    INNER JOIN produtos p ON p.id = pv.produto_id
                    WHERE pv.id = $variacao_id";

            $result = $conn->query($sql);
            $dados = $result->fetch_assoc();

            $produto_id = $dados['produto_id'];
            $preco = $dados['preco'];
            $subtotal = $preco * $quantidade;

            // INSERT ITEM
            $sql = "INSERT INTO itens_pedido
                    (pedido_id, produto_id, variacao_id, quantidade, preco_unitario, subtotal)
                    VALUES
                    ($pedido_id, $produto_id, $variacao_id, $quantidade, $preco, $subtotal)";

            if (!$conn->query($sql)) {
                throw new Exception("Erro ao inserir item");
            }

            // UPDATE ESTOQUE
            $sql = "UPDATE produto_variacoes
                    SET estoque = estoque - $quantidade
                    WHERE id = $variacao_id";

            if (!$conn->query($sql)) {
                throw new Exception("Erro ao atualizar estoque");
            }
        }

        // =========================
        // 6. FINALIZAR
        // =========================
        // $conn->commit();

        unset($_SESSION['carrinho']);

        echo "<script>alert('Compra realizada com sucesso ✔️');</script>";

    } catch (Exception $e) {

        // $conn->rollback();

        echo "<script>alert('Erro: " . $e->getMessage() . "');</script>";
    }
}

// =============================
// FRETE
// =============================
// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['calcular_frete'])) {
//     $estado = $_POST['estado'] ?? '';
//     $peso = $_POST['peso'] ?? 1;

//     if (!$estado || $peso <= 0) {
//         $resultado = ['erro' => "Dados inválidos."];
//     } else {
//         $resultado = calcularFreteSimulado($estado, $peso);
//         $valorFrete = $resultado['valor_numerico'] ?? 0;
//     }
// }

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
                        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>

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
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="firstName" class="form-label">Nome</label>
                                <input type="text" class="form-control" name="nome" id="firstName" placeholder="" value="<?php echo $_POST['nome'] ?? '' ?>" required>
                                <div class="invalid-feedback">
                                    Valid first name is required.
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="lastName" class="form-label">Sobrenome</label>
                                <input type="text" class="form-control" name="sobrenome" id="lastName" placeholder="" value="<?php echo $_POST['sobrenome'] ?? '' ?>" required>
                                <div class="invalid-feedback">
                                    Valid last name is required.
                                </div>
                            </div>
	                            <div class="col-12">
	                                <label for="email" class="form-label">Email</label>
	                                <input type="email" class="form-control" name="email" id="email" placeholder="nome@exemplo.com" value="<?php echo $_POST['email'] ?? '' ?>"
	                                    required>
	                                <div class="invalid-feedback">
	                                    Please enter a valid email address for shipping updates.
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
                                    class="form-control" name="cep" id="cep" placeholder="CEP" onblur="buscaCEP()" value="<?php echo $_POST['cep'] ?? '' ?>" required>
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="col-8">
                                <label for="address" class="form-label">Endereço</label>
                                <input type="text"
                                    class="form-control" name="rua" id="logradouro" placeholder="Rua" value="<?php echo $_POST['rua'] ?? '' ?>" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="numero" class="form-label">Numero</label>
                                <input type="text"
                                    class="form-control" name="numero" id="numero" placeholder="Numero" value="<?php echo $_POST['numero'] ?? '' ?>" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-5">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input type="text"
                                    class="form-control" name="complemento" id="complemento" placeholder="Apartamento, bloco, casa..." value="<?php echo $_POST['complemento'] ?? '' ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-4"> <label for="address" class="form-label">Bairro</label>
                                <input type="text"
                                    class="form-control" name="bairro" id="bairro" placeholder="Bairro" value="<?php echo $_POST['bairro'] ?? '' ?>" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-4"> <label for="address" class="form-label">Cidade</label>
                                <input type="text"
                                    class="form-control" name="cidade" id="localidade" placeholder="Cidade" value="<?php echo $_POST['cidade'] ?? '' ?>" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-2"> <label for="address" class="form-label">Estado</label>
                                <input type="text"
                                    class="form-control" name="estado" id="uf" placeholder="Estado" value="<?php echo $_POST['estado'] ?? '' ?>" required>
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="country" class="form-label">País</label>
                                <select class="form-select" name="pais" id="country" required>
                                    <!-- A opção padrão deve ter value vazio para o 'required' funcionar -->
                                    <option value="" <?php echo !isset($_POST['pais']) ? 'selected' : ''; ?> disabled>Selecionar</option>
                                    
                                    <option value="United States" <?php echo (($_POST['pais'] ?? '') == 'United States') ? 'selected' : ''; ?>>United States</option>
                                    
                                    <option value="Brasil" <?php echo (($_POST['pais'] ?? '') == 'Brasil') ? 'selected' : ''; ?>>Brasil</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a valid country.
                                </div>
                            </div>
                            <input type="hidden" name="frete" id="freteInput" value="0">

                           <button type="button" id="btnCalcularFrete" class="btn btn-secondary">
                                Calcular frete
                            </button>

                            
                        <!-- <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to checkout</button> -->

                             <input type="hidden" name="carrinho" id="carrinhoInput">
                            <hr class="my-4">
                            <h4 class="mb-3">Pagamento</h4>
                            <div class="my-3">
                                <div class="form-check">
                                    <input id="credit" name="metodo_pagamento" type="radio" class="form-check-input" value="credit" required>
                                    <label class="form-check-label" for="credit">Cartão de crédito</label>
                                </div>

                                <div class="form-check">
                                    <input id="debit" name="metodo_pagamento" type="radio" class="form-check-input" value="debit" required>
                                    <label class="form-check-label" for="debit">Cartão de débito</label>
                                </div>

                                <div class="form-check">
                                    <input id="pix-Radio" name="metodo_pagamento" type="radio" class="form-check-input" value="pix" required>
                                    <label class="form-check-label" for="pix">Pix</label>
                                </div>
                            </div>

                            <div class="row gy-3" id="cardPaymentFields" style="display: none;">
                                <div class="col-md-6">
                                    <label for="cc-name" class="form-label">Nome no cartão</label>
                                    <input type="text" class="form-control" id="cc-name" placeholder="">
                                    <small class="text-body-secondary">Nome completo como está no cartão</small>
                                    <div class="invalid-feedback">
                                        Name on card is required
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="cc-number" class="form-label">Número do cartão</label>
                                    <input type="text" class="form-control" id="cc-number" placeholder="">
                                    <div class="invalid-feedback">
                                        Credit card number is required
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="cc-expiration" class="form-label">Expiração</label>
                                    <input type="text" class="form-control" id="cc-expiration" placeholder="">
                                    <div class="invalid-feedback">
                                        Expiration date required
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="cc-cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cc-cvv" placeholder="">
                                    <div class="invalid-feedback">
                                        Security code required
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
	                            <button class="w-100 btn btn-primary btn-lg mt-3" name="finalizar_compra" value="1" id="checkoutSubmitPayment" type="submit">
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

                document.getElementById('logradouro').value = data.logradouro;
                document.getElementById('bairro').value = data.bairro;
                document.getElementById('localidade').value = data.localidade;
                document.getElementById('uf').value = data.uf;

            } catch (error) {
                console.error("Erro ao buscar o CEP:", error);
            }
        }

        document.getElementById('cep').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                buscaCEP();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/checkout.js"></script>

    <script>
        document.getElementById("btnCalcularFrete").addEventListener("click", async () => {
            const shippingElement = document.getElementById("checkoutShippingCost");
            const estado = document.getElementById("uf").value;
            const peso = 1;

            if (!estado) {
                alert("Informe o estado primeiro");
                return;
            }

            try {
                const response = await fetch("frete.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `estado=${encodeURIComponent(estado)}&peso=${peso}`
                });

                const data = await response.json();

                if (data.erro) {
                    alert(data.erro);
                    return;
                }

                // Atualiza texto
                shippingElement.textContent = "R$ " + data.valor;

                // Atualiza dataset (ESSENCIAL)
                shippingElement.dataset.shippingCost = data.valor_numerico;

                // Atualiza hidden input
                document.getElementById("freteInput").value = data.valor_numerico;

            } catch (err) {
                console.error(err);
                alert("Erro ao calcular frete");
            }
        });
    </script>

        
</body>


<div id="modalPixContainer" class="modal">
    <div class="modal-dialog d-flex justify-content-center align-items-center">
        <div class="modal-content text-center p-4">
            <span class="close-btn" onclick="fecharModal()" style="position: absolute; right: 20px; top: 15px; font-size: 28px; cursor: pointer;">&times;</span>

            <h2 class="modal-title mb-3">Pagamento PIX</h2>

            <div class="qr-container mb-3">
                <img src="../clothing/assets/img/testeqrcode.jpg" alt="QR Code PIX"
                    style="width: 100%; max-width: 250px; height: auto; margin: 0 auto; display: block; border: 1px solid #eee; padding: 15px; background: #fff;">
                <p class="qr-instruction mt-2 mb-3" style="font-size: 15px; color: #666;">Aponte a câmera do seu banco para o código acima</p>
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

</html>

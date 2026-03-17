<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body class="container py-4">
    <h1 class="h2">Formulário de cadastro</h1>
    <p class="lead">Informe seus dados para uma melhor experiência em nosso site.</p>

    <div class="row g-5">

        <div class="col-md-5 col-lg-4 order-md-last">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Seu carrinho</span>
                <span class="badge bg-primary rounded-pill">3</span>
            </h4>
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">Nome do produto</h6>
                        <small class="text-body-secondary">descrição</small>
                    </div>
                    <span class="text-body-secondary">$12</span>
                </li>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">Segundo produto</h6>
                        <small class="text-body-secondary">descrição</small>
                    </div>
                    <span class="text-body-secondary">$8</span>
                </li>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">Terceiro produto</h6>
                        <small class="text-body-secondary">descrição</small>
                    </div>
                    <span class="text-body-secondary">$5</span>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-body-tertiary">
                    <div class="text-success">
                        <h6 class="my-0">Código de desconto</h6>
                        <small>código exemplo</small>
                    </div>
                    <span class="text-success">−$5</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total (R$)</span>
                    <strong>$20</strong>
                </li>
            </ul>
            <form class="card p-2">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Código de desconto">
                    <button type="submit" class="btn btn-secondary">Resgatar</button>
                </div>
            </form>
        </div>


        <div class="col-md-7 col-lg-8">
            <form class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label for="firstName" class="form-label">Primeiro nome</label>
                        <input type="text" class="form-control" id="firstName" placeholder="" value="" required>
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <label for="lastName" class="form-label">Sobrenome</label>
                        <input type="text" class="form-control" id="lastName" placeholder="" value="" required>
                        <div class="invalid-feedback">
                            Valid last name is required.
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="username" class="form-label">Nome do usuário</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">@</span>
                            <input type="text" class="form-control" id="username" placeholder="Username" required>
                            <div class="invalid-feedback">
                                Your username is required.
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="nome@exemplo.com" value=""
                            required>
                        <div class="invalid-feedback">
                            Please enter a valid email address for shipping updates.
                        </div>
                    </div>

                    <div class="col-4">
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" class="form-control" id="cep" placeholder="" onblur="buscaCEP()" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-8">
                        <label for="logradouro" class="form-label">Endereço</label>
                        <input type="text" class="form-control" id="logradouro" placeholder="" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-5">
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" class="form-control" id="bairro" placeholder="" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-5">
                        <label for="localidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="localidade" placeholder="" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-2">
                        <label for="uf" class="form-label">Estado</label>
                        <input type="text" class="form-control" id="uf" placeholder="" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-12">
                        <label for="address2" class="form-label">2° Endereço <span
                                class="text-body-secondary">(Opcional)</span></label>
                        <input type="text" class="form-control" id="address2" placeholder="Apartamento ou suíte">
                    </div>

                    <div class="col-md-5">
                        <label for="country" class="form-label">País</label>
                        <select class="form-select" id="country" required>
                            <option value="">Selecionar...</option>
                            <option>United States</option>
                            <option>Brasil</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a valid country.
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="same-address">
                    <label class="form-check-label" for="same-address">O endereço de entrega é o mesmo que o meu
                        endereço de cobrança.</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="save-info">
                    <label class="form-check-label" for="save-info">Guarde esta informação para a próxima vez.</label>
                </div>

                <hr class="my-4">

                <h4 class="mb-3">Pagamento</h4>
                <div class="my-3">
                    <div class="form-check">
                        <input id="credit" name="paymentMethod" type="radio" class="form-check-input" checked required>
                        <label class="form-check-label" for="credit">Cartão de crédito</label>
                    </div>

                    <div class="form-check">
                        <input id="debit" name="paymentMethod" type="radio" class="form-check-input" required>
                        <label class="form-check-label" for="debit">Cartão de débito</label>
                    </div>

                    <div class="form-check">
                        <input id="pix-Radio" name="paymentMethod" type="radio" class="form-check-input" required>
                        <label class="form-check-label" for="pix">Pix</label>
                    </div>
                </div>

                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="cc-name" class="form-label">Nome no cartão</label>
                        <input type="text" class="form-control" id="cc-name" placeholder="" required>
                        <small class="text-body-secondary">Nome completo como está no cartão</small>
                        <div class="invalid-feedback">
                            Name on card is required
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="cc-number" class="form-label">Número do cartão</label>
                        <input type="text" class="form-control" id="cc-number" placeholder="" required>
                        <div class="invalid-feedback">
                            Credit card number is required
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="cc-expiration" class="form-label">Expiração</label>
                        <input type="text" class="form-control" id="cc-expiration" placeholder="" required>
                        <div class="invalid-feedback">
                            Expiration date required
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="cc-cvv" class="form-label">CVV</label>
                        <input type="text" class="form-control" id="cc-cvv" placeholder="" required>
                        <div class="invalid-feedback">
                            Security code required
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <button class="w-100 btn btn-primary btn-lg" type="submit">
                    Continuar para o pagamento
                </button>
            </form>
        </div>
    </div>

    <footer class="my-5 pt-5 text-body-secondary text-center text-small">
        <p class="mb-1">&copy; 2017–2025 Company Name</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#">Privacy</a></li>
            <li class="list-inline-item"><a href="#">Terms</a></li>
            <li class="list-inline-item"><a href="#">Support</a></li>
        </ul>
    </footer>


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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../clothing/assets/js/checkout.js"></script>
</body>

</html>
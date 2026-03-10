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
    </div>
    <div class="row g-5">
        <div class="col-md-5 col-lg-4 order-md-last">
            <h4 class="d-flex justify-content-between align-items-center mb-3"> <span class="text-primary">Your
                    cart</span> <span class="badge bg-primary rounded-pill">3</span> </h4>
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">Product name</h6> <small class="text-body-secondary">Brief description</small>
                    </div> <span class="text-body-secondary">$12</span>
                </li>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">Second product</h6> <small class="text-body-secondary">Brief
                            description</small>
                    </div> <span class="text-body-secondary">$8</span>
                </li>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">Third item</h6> <small class="text-body-secondary">Brief description</small>
                    </div> <span class="text-body-secondary">$5</span>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-body-tertiary">
                    <div class="text-success">
                        <h6 class="my-0">Promo code</h6> <small>EXAMPLECODE</small>
                    </div> <span class="text-success">−$5</span>
                </li>
                <li class="list-group-item d-flex justify-content-between"> <span>Total (USD)</span>
                    <strong>$20</strong> </li>
            </ul>
            <form class="card p-2">
                <div class="input-group"> <input type="text" class="form-control" placeholder="Promo code"> <button
                        type="submit" class="btn btn-secondary">Redeem</button> </div>
            </form>
        </div>
        <div class="col-md-7 col-lg-8">
            <form class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-sm-6"> <label for="firstName" class="form-label">Primeiro nome</label> <input
                            type="text" class="form-control" id="firstName" placeholder="" value="" required>
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                    </div>
                    <div class="col-sm-6"> <label for="lastName" class="form-label">Sobrenome</label> <input type="text"
                            class="form-control" id="lastName" placeholder="" value="" required>
                        <div class="invalid-feedback">
                            Valid last name is required.
                        </div>
                    </div>
                    <div class="col-12"> <label for="username" class="form-label">Nome do usuário</label>
                        <div class="input-group has-validation"> <span class="input-group-text">@</span> <input
                                type="text" class="form-control" id="username" placeholder="Username" required>
                            <div class="invalid-feedback">
                                Your username is required.
                            </div>
                        </div>
                    </div>
                    <div class="col-12"> <label for="email" class="form-label">Email <span class="text-body-secondary">
                            </span></label> <input type="email" class="form-control" id="email"
                            placeholder="you@example.com" value="" required>
                        <div class="invalid-feedback">
                            Please enter a valid email address for shipping updates.
                        </div>
                    </div>

                     <div class="col-4"> <label for="address" class="form-label">CEP</label> <input type="text"
                            class="form-control" id="cep" placeholder="CEP" onblur="buscaCEP()" required>
                        <div class="invalid-feedback">
                        </div>
                    </div>
                    <div class="col-8"> <label for="address" class="form-label">Endereço</label> <input type="text"
                            class="form-control" id="logradouro" placeholder="Rua" required>
                        <div class="invalid-feedback">

                        </div>
                    </div>
                    <div class="col-5"> <label for="address" class="form-label">Bairro</label> <input type="text"
                            class="form-control" id="bairro" placeholder="Bairro" required>
                        <div class="invalid-feedback">

                        </div>
                    </div>
                    <div class="col-5"> <label for="address" class="form-label">Cidade</label> <input type="text"
                            class="form-control" id="localidade" placeholder="Cidade" required>
                        <div class="invalid-feedback">

                        </div>
                    </div>
                    <div class="col-2"> <label for="address" class="form-label">Estado</label> <input type="text"
                            class="form-control" id="uf" placeholder="Estado" required>
                        <div class="invalid-feedback">


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
</script>

                        </div>
                    </div>
                    <div class="col-12"> <label for="address2" class="form-label">2° Endereço <span
                                class="text-body-secondary">(Opcional)</span></label> <input type="text"
                            class="form-control" id="address2" placeholder="Apartment or suite"> </div>
                    <div class="col-md-5"> <label for="country" class="form-label">País</label> <select
                            class="form-select" id="country" required>
                            <option value="">Selecionar...</option>
                            <option>United States</option>
                            <option>Brasil</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a valid country.
                        </div>
                    </div>
                        </select>
                        <div class="invalid-feedback">
                            Please provide a valid state.
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="form-check"> <input type="checkbox" class="form-check-input" id="same-address"> <label
                        class="form-check-label" for="same-address">O endereço de entrega é o mesmo que o meu endereço
                        de cobrança.</label> </div>
                <div class="form-check"> <input type="checkbox" class="form-check-input" id="save-info"> <label
                        class="form-check-label" for="save-info">Guarde esta informação para a próxima vez.</label>
                </div>
                <hr class="my-4">
                <h4 class="mb-3">Pagamento</h4>
                <div class="my-3">
                    <div class="form-check"> <input id="credit" name="paymentMethod" type="radio"
                            class="form-check-input" checked required> <label class="form-check-label"
                            for="credit">Cartão de crédito</label> </div>
                    <div class="form-check"> <input id="debit" name="paymentMethod" type="radio"
                            class="form-check-input" required> <label class="form-check-label" for="debit">Cartão de
                            débito</label> </div>
                    <div class="form-check"> <input id="paypal" name="paymentMethod" type="radio"
                            class="form-check-input" required> <label class="form-check-label"
                            for="paypal">PayPal</label> </div>
                </div>
                <div class="row gy-3">
                    <div class="col-md-6"> <label for="cc-name" class="form-label">Name on card</label> <input
                            type="text" class="form-control" id="cc-name" placeholder="" required> <small
                            class="text-body-secondary">Full name as displayed on card</small>
                        <div class="invalid-feedback">
                            Name on card is required
                        </div>
                    </div>
                    <div class="col-md-6"> <label for="cc-number" class="form-label">Número do cartão</label> <input
                            type="text" class="form-control" id="cc-number" placeholder="" required>
                        <div class="invalid-feedback">
                            Credit card number is required
                        </div>
                    </div>
                    <div class="col-md-3"> <label for="cc-expiration" class="form-label">Expiration</label> <input
                            type="text" class="form-control" id="cc-expiration" placeholder="" required>
                        <div class="invalid-feedback">
                            Expiration date required
                        </div>
                    </div>
                    <div class="col-md-3"> <label for="cc-cvv" class="form-label">CVV</label> <input type="text"
                            class="form-control" id="cc-cvv" placeholder="" required>
                        <div class="invalid-feedback">
                            Security code required
                        </div>
                    </div>
                </div>
                <hr class="my-4"> <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to
                    checkout</button>
            </form>
        </div>
    </div>
    </main>
    <footer class="my-5 pt-5 text-body-secondary text-center text-small">
        <p class="mb-1">&copy; 2017–2025 Company Name</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#">Privacy</a></li>
            <li class="list-inline-item"><a href="#">Terms</a></li>
            <li class="list-inline-item"><a href="#">Support</a></li>
        </ul>
    </footer>
    </div>
    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        class="astro-vvvwv3sm"></script>
    <script src="checkout.js" class="astro-vvvwv3sm"></script>
</body>

</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>
</body>

</html>
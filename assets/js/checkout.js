async function buscaCEP() {
    const cep = document.getElementById('cep').value.replace(/\D/g, '');
    
    if (cep.length !== 8) return;

    try {
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const data = await response.json();

        if (data.erro) {
            Swal.fire({
                icon: "error",
                title: "CEP não encontrado",
                text: "Verifique o CEP informado e tente novamente."
            });
            return;
        }

        document.getElementById('logradouro').value = data.logradouro;
        document.getElementById('bairro').value = data.bairro;
        document.getElementById('localidade').value = data.localidade;
        document.getElementById('uf').value = data.uf;
        
    } catch (error) {
        console.error("Erro ao buscar o CEP:", error);
        Swal.fire({
            icon: "error",
            title: "Erro",
            text: "Não foi possível buscar o CEP."
        });
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("modalPixContainer");
    const pixRadio = document.getElementById("pix-Radio");
    const creditRadio = document.getElementById("credit");
    const debitRadio = document.getElementById("debit");
    const cardPaymentFields = document.getElementById("cardPaymentFields");
    const cardInputs = cardPaymentFields ? cardPaymentFields.querySelectorAll("input") : [];
    const couponForm = document.getElementById("checkoutCouponForm");
    const couponInput = couponForm ? couponForm.querySelector('input[type="text"]') : null;
    const checkoutForm = document.getElementById("checkoutForm");
    const CHECKOUT_COUPON_KEY = "checkoutCouponCode";

    const spanClose = document.querySelector(".close-btn") || document.querySelector(".close");

    if (checkoutForm) {
        checkoutForm.addEventListener("submit", function () {
            const cart = getCartItems();
            const carrinhoInput = document.getElementById("carrinhoInput");

            if (carrinhoInput) {
                carrinhoInput.value = JSON.stringify(cart);
            }
        });
    }
    
    function getAppliedCouponCode() {
        return localStorage.getItem(CHECKOUT_COUPON_KEY) || "";
    }

    function saveAppliedCouponCode(couponCode) {
        if (!couponCode) {
            localStorage.removeItem(CHECKOUT_COUPON_KEY);
            return;
        }

        localStorage.setItem(CHECKOUT_COUPON_KEY, couponCode);
    }

    saveAppliedCouponCode("");

    function renderCheckoutCartSummary() {
        const cartItemsContainer = document.getElementById("checkoutCartItems");
        const cartCountBadge = document.getElementById("checkoutCartCount");
        const cartTotalElement = document.getElementById("checkoutCartTotal");
        const shippingCostElement = document.getElementById("checkoutShippingCost");
        const discountRow = document.getElementById("checkoutDiscountRow");
        const discountValueElement = document.getElementById("checkoutDiscountValue");

        if (!cartItemsContainer || !cartCountBadge || !cartTotalElement || !shippingCostElement || !discountRow || !discountValueElement || typeof getCartItems !== "function") {
            return;
        }

        const cartItems = getCartItems();
        const totalItems = typeof getCartItemsCount === "function" ? getCartItemsCount() : 0;
        const cartTotal = typeof getCartTotal === "function" ? getCartTotal() : 0;
        const shippingCost = Number(shippingCostElement.dataset.shippingCost || 0);
        const subtotalWithShipping = cartTotal + shippingCost;
        const appliedCoupon = getAppliedCouponCode().toLowerCase();
        const discountAmount = appliedCoupon === "senac20" ? subtotalWithShipping * 0.2 : 0;
        const finalTotal = Math.max(0, subtotalWithShipping - discountAmount);
        const totalFormatted = typeof formatCurrency === "function"
            ? formatCurrency(finalTotal)
            : `R$ ${finalTotal.toFixed(2).replace(".", ",")}`;
        const discountFormatted = typeof formatCurrency === "function"
            ? formatCurrency(discountAmount)
            : `R$ ${discountAmount.toFixed(2).replace(".", ",")}`;

        cartCountBadge.textContent = totalItems;
        cartTotalElement.textContent = totalFormatted;
        discountRow.style.display = discountAmount > 0 ? "flex" : "none";
        discountValueElement.textContent = `- ${discountFormatted}`;

        if (!cartItems.length) {
            cartItemsContainer.innerHTML = '<p class="header-panel-empty mb-0">Seu carrinho esta vazio.</p>';
            return;
        }

        cartItemsContainer.innerHTML = cartItems.map(function(item) {
            const itemQuantity = Number(item.quantity) || 1;
            const itemPrice = typeof parsePriceValue === "function" ? parsePriceValue(item.price) : 0;
            const subtotal = itemPrice * itemQuantity;
            const product = typeof getProductData === "function" ? getProductData(item.id) || {} : {};
            const image = item.image || product.image || "";
            const name = item.name || product.name || "Produto indisponivel";
            const url = item.url || product.url || `produto.php?id=${encodeURIComponent(item.id)}`;
            const imageMarkup = image
                ? `<img src="${image}" alt="${name}" class="header-panel-thumb">`
                : '<div class="header-panel-thumb header-panel-thumb--placeholder"></div>';
            const subtotalFormatted = typeof formatCurrency === "function"
                ? formatCurrency(subtotal)
                : `R$ ${subtotal.toFixed(2).replace(".", ",")}`;

            return `
                <div class="header-panel-item">
                    <a href="${url}" class="header-panel-link">
                        ${imageMarkup}
                        <div class="header-panel-content">
                            <p class="header-panel-title mb-1">${name}</p>
                            <p class="header-panel-price mb-0">${subtotalFormatted}</p>
                        </div>
                    </a>
                    <div class="header-panel-actions">
                        <div class="header-quantity-control" aria-label="Alterar quantidade">
                            <button type="button" class="header-quantity-btn" data-cart-quantity-action="decrease" data-cart-item-id="${item.id}" aria-label="Diminuir quantidade">
                                -
                            </button>
                            <span class="header-quantity-value">${itemQuantity}</span>
                            <button type="button" class="header-quantity-btn" data-cart-quantity-action="increase" data-cart-item-id="${item.id}" aria-label="Aumentar quantidade">
                                +
                            </button>
                        </div>
                        <button type="button" class="header-panel-remove" data-remove-cart-item="${item.id}">
                            Remover
                        </button>
                    </div>
                </div>
            `;
        }).join("");

        cartItemsContainer.innerHTML = `<div class="header-panel-list">${cartItemsContainer.innerHTML}</div>`;
    }

    if (couponInput) {
        couponInput.value = "";
    }

    if (couponForm) {
        couponForm.addEventListener("submit", function(event) {
            event.preventDefault();

            const couponCode = couponInput ? couponInput.value.trim().toLowerCase() : "";

            if (couponCode === "senac20") {
                saveAppliedCouponCode(couponCode);
                renderCheckoutCartSummary();
                Swal.fire({
                    icon: "success",
                    title: "Cupom aplicado",
                    text: "O desconto de 20% foi aplicado no valor total."
                });
                return;
            }

            saveAppliedCouponCode("");
            renderCheckoutCartSummary();
            Swal.fire({
                icon: "error",
                title: "Cupom invalido",
                text: "O codigo informado nao e valido."
            });
        });
    }

    if (checkoutForm) {
        const btn = document.getElementById("checkoutSubmitPayment");
        if(btn) {
             btn.addEventListener("click", function (event) {
                console.log("Clique no botão pagar");

                const form = document.getElementById("checkoutForm");
                if (!form.checkValidity()) {
                    event.preventDefault();

                    Swal.fire({
                        icon: "error",
                        title: "Formulario incompleto",
                        text: "Preencha os campos obrigatorios."
                    });

                    return;
                }
                Swal.fire({
                    title: "Processando pagamento...",
                    text: "Aguarde enquanto confirmamos seu pagamento.",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: function() {
                        Swal.showLoading();
                    }
                }).then(function(result) {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        Swal.fire({
                            icon: "success",
                            title: "Pagamento realizado com sucesso",
                            text: "Seu pagamento foi concluido."
                        });
                    }
                });
                console.log("Form válido, enviando");
            });
        }
    }

    function toggleCardFields(showCardFields) {
        if (!cardPaymentFields) {
            return;
        }

        cardPaymentFields.style.display = showCardFields ? "flex" : "none";

        cardInputs.forEach(function(input) {
            input.required = showCardFields;

            if (!showCardFields) {
                input.value = "";
            }
        });
    }

    if (creditRadio) {
        creditRadio.addEventListener("change", function() {
            if (this.checked) {
                fecharModal();
                toggleCardFields(true);
            }
        });
    }

    if (debitRadio) {
        debitRadio.addEventListener("change", function() {
            if (this.checked) {
                fecharModal();
                toggleCardFields(true);
            }
        });
    }

    if (pixRadio) {
        pixRadio.addEventListener("change", function() {
            if (this.checked) {
                toggleCardFields(false);
                if (modal) {
                    modal.style.display = "block";
                    document.body.style.overflow = "hidden";
                }
            }
        });
    }

    if (spanClose) {
        spanClose.onclick = function() {
            fecharModal();
        }
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            fecharModal();
        }
    }

    toggleCardFields(false);
    renderCheckoutCartSummary();

    document.addEventListener("cart:updated", renderCheckoutCartSummary);
    window.addEventListener("storage", renderCheckoutCartSummary);
});

function fecharModal() {
    const modal = document.getElementById("modalPixContainer");
    if (!modal) {
        return;
    }

    modal.style.display = "none";
    document.body.style.overflow = "auto";
}

function enviarDados() {
    const arquivoInput = document.getElementById('comprovante');
    const arquivo = arquivoInput.files[0];
    
    if (!arquivo) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Por favor, selecione o comprovante antes de confirmar."
        });
        return;
    }

    Swal.fire({
        title: "Pagamento enviado!",
        text: "O comprovante " + arquivo.name + " foi enviado para análise.",
        icon: "success"
    });

    arquivoInput.value = "";
    fecharModal();
}

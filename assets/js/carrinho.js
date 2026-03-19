const CART_STORAGE_KEY = 'shoppingCart';

function getCartItems() {
  const savedCart = localStorage.getItem(CART_STORAGE_KEY);

  if (!savedCart) {
    return [];
  }

  try {
    const parsedCart = JSON.parse(savedCart);
    return Array.isArray(parsedCart) ? parsedCart : [];
  } catch (error) {
    return [];
  }
}

function saveCartItems(cartItems) {
  localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cartItems));
}

function removeProductFromCart(productId) {
  if (!productId) {
    return;
  }

  const updatedCartItems = getCartItems().filter((item) => item.id !== productId);
  saveCartItems(updatedCartItems);
  document.dispatchEvent(new CustomEvent('cart:updated'));
}

function getCartItemsCount() {
  return getCartItems().reduce((total, item) => {
    const quantity = Number(item.quantity) || 0;
    return total + quantity;
  }, 0);
}

function parsePriceValue(price) {
  if (!price) {
    return 0;
  }

  const normalizedPrice = String(price).replace(/\./g, '').replace(',', '.');
  const parsedPrice = Number(normalizedPrice);
  return Number.isNaN(parsedPrice) ? 0 : parsedPrice;
}

function formatCurrency(value) {
  return `R$ ${value.toFixed(2).replace('.', ',')}`;
}

function getCartTotal() {
  return getCartItems().reduce((total, item) => {
    const price = parsePriceValue(item.price);
    const quantity = Number(item.quantity) || 0;
    return total + (price * quantity);
  }, 0);
}

function updateCartCountBadge() {
  const cartCountElements = document.querySelectorAll('[data-cart-count]');
  const totalItems = getCartItemsCount();

  cartCountElements.forEach((element) => {
    element.textContent = totalItems;
    element.style.display = totalItems > 0 ? 'inline-block' : 'none';
  });
}

function updateCartTotal() {
  const cartTotalElements = document.querySelectorAll('[data-cart-total]');
  const totalValue = formatCurrency(getCartTotal());

  cartTotalElements.forEach((element) => {
    element.textContent = totalValue;
  });
}

function renderCartItems() {
  const cartListElements = document.querySelectorAll('[data-cart-list]');
  const cartItems = getCartItems();

  cartListElements.forEach((element) => {
    if (!cartItems.length) {
      element.innerHTML = '<p class="header-panel-empty mb-0">Seu carrinho esta vazio.</p>';
      return;
    }

    const cartMarkup = cartItems.map((item) => {
      const product = getProductData(item.id) || {};
      const image = item.image || product.image || '';
      const name = item.name || product.name || 'Produto indisponivel';
      const url = product.url || '#';
      const price = item.price || product.price || '';
      const imageMarkup = image
        ? `<img src="${image}" alt="${name}" class="header-panel-thumb">`
        : '<div class="header-panel-thumb header-panel-thumb--placeholder"></div>';
      const priceMarkup = price
        ? `<p class="header-panel-price mb-0">R$ ${price}</p>`
        : '';

      return `
        <div class="header-panel-item">
          <a href="${url}" class="header-panel-link">
            ${imageMarkup}
            <div class="header-panel-content">
              <p class="header-panel-title mb-1">${name}</p>
              <p class="header-panel-meta mb-1">Quantidade: ${item.quantity}</p>
              ${priceMarkup}
            </div>
          </a>
          <button type="button" class="header-panel-remove" data-remove-cart-item="${item.id}">
            Remover
          </button>
        </div>
      `;
    }).join('');

    element.innerHTML = `<div class="header-panel-list">${cartMarkup}</div>`;
  });
}

function addProductToCart(product) {
  if (!product || !product.id) {
    return;
  }

  const cartItems = getCartItems();
  const quantityToAdd = Math.max(1, Number(product.quantity) || 1);
  const existingItem = cartItems.find((item) => item.id === product.id);

  if (existingItem) {
    existingItem.quantity += quantityToAdd;
  } else {
    cartItems.push({
      id: product.id,
      name: product.name || '',
      price: product.price || '',
      image: product.image || '',
      quantity: quantityToAdd
    });
  }

  saveCartItems(cartItems);
  document.dispatchEvent(new CustomEvent('cart:updated'));
}

document.addEventListener('DOMContentLoaded', updateCartCountBadge);
document.addEventListener('DOMContentLoaded', renderCartItems);
document.addEventListener('DOMContentLoaded', updateCartTotal);
document.addEventListener('click', (event) => {
  const removeButton = event.target.closest('[data-remove-cart-item]');

  if (!removeButton) {
    return;
  }

  removeProductFromCart(removeButton.dataset.removeCartItem);
});
document.addEventListener('cart:updated', updateCartCountBadge);
document.addEventListener('cart:updated', renderCartItems);
document.addEventListener('cart:updated', updateCartTotal);
window.addEventListener('storage', updateCartCountBadge);
window.addEventListener('storage', renderCartItems);
window.addEventListener('storage', updateCartTotal);

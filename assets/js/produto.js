function updateFavoriteButton(button, isActive) {
  const icon = button.querySelector('.favorite-btn__icon');

  button.classList.toggle('is-active', isActive);
  button.setAttribute(
    'aria-label',
    isActive ? 'Remover dos favoritos' : 'Favoritar produto'
  );

  if (icon) {
    icon.innerHTML = isActive ? '&#9829;' : '&#9825;';
  }
}

function normalizeQuantity(value) {
  const parsedValue = parseInt(value, 10);
  return Number.isNaN(parsedValue) || parsedValue < 1 ? 1 : parsedValue;
}

function toggleFavorite(productId) {
  const favorites = getFavoriteProducts();
  const isFavorite = favorites.includes(productId);

  const updatedFavorites = isFavorite
    ? favorites.filter((id) => id !== productId)
    : [...favorites, productId];

  saveFavoriteProducts(updatedFavorites);
  document.dispatchEvent(new CustomEvent('favorites:updated'));

  return !isFavorite;
}

document.querySelectorAll('.favorite-btn[data-product-id]').forEach((button) => {
  const productId = button.dataset.productId;

  if (!productId) {
    return;
  }

  const favorites = getFavoriteProducts();
  updateFavoriteButton(button, favorites.includes(productId));

  button.addEventListener('click', () => {
    const isActive = toggleFavorite(productId);
    updateFavoriteButton(button, isActive);
  });
});

const quantityInput = document.querySelector('[data-product-quantity]');

if (quantityInput) {
  const syncQuantityValue = (value) => {
    quantityInput.value = normalizeQuantity(value);
  };

  document.querySelectorAll('[data-quantity-action]').forEach((button) => {
    button.addEventListener('click', () => {
      const currentQuantity = normalizeQuantity(quantityInput.value);
      const nextQuantity = button.dataset.quantityAction === 'decrease'
        ? currentQuantity - 1
        : currentQuantity + 1;

      syncQuantityValue(nextQuantity);
    });
  });

  quantityInput.addEventListener('input', () => {
    quantityInput.value = quantityInput.value.replace(/\D/g, '');
  });

  quantityInput.addEventListener('blur', () => {
    syncQuantityValue(quantityInput.value);
  });
}

const addToCartButton = document.querySelector('[data-add-to-cart]');

if (addToCartButton) {
  addToCartButton.addEventListener('click', () => {
    const quantity = quantityInput ? normalizeQuantity(quantityInput.value) : 1;

    addProductToCart({
      id: addToCartButton.dataset.productId,
      name: addToCartButton.dataset.productName,
      price: addToCartButton.dataset.productPrice,
      image: addToCartButton.dataset.productImage,
      quantity
    });
  });
}

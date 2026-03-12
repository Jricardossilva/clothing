const FAVORITES_STORAGE_KEY = 'favoriteProducts';

function getFavoriteProducts() {
  const savedFavorites = localStorage.getItem(FAVORITES_STORAGE_KEY);

  if (!savedFavorites) {
    return [];
  }

  try {
    const parsedFavorites = JSON.parse(savedFavorites);
    return Array.isArray(parsedFavorites) ? parsedFavorites : [];
  } catch (error) {
    return [];
  }
}

function saveFavoriteProducts(favorites) {
  localStorage.setItem(FAVORITES_STORAGE_KEY, JSON.stringify(favorites));
}

function updateFavoritesCountBadge() {
  const favoritesCountElements = document.querySelectorAll('[data-favorites-count]');
  const favorites = getFavoriteProducts();
  const totalFavorites = favorites.length;

  favoritesCountElements.forEach((element) => {
    element.textContent = totalFavorites;
    element.style.display = totalFavorites > 0 ? 'inline-block' : 'none';
  });
}

function renderFavoritesList() {
  const favoritesListElements = document.querySelectorAll('[data-favorites-list]');
  const favorites = getFavoriteProducts();

  favoritesListElements.forEach((element) => {
    if (!favorites.length) {
      element.innerHTML = '<p class="header-panel-empty mb-0">Nenhum produto favoritado ainda.</p>';
      return;
    }

    const favoritesMarkup = favorites.map((productId) => {
      const product = getProductData(productId) || {
        name: 'Produto indisponivel',
        price: '',
        image: '',
        url: '#'
      };

      const imageMarkup = product.image
        ? `<img src="${product.image}" alt="${product.name}" class="header-panel-thumb">`
        : '<div class="header-panel-thumb header-panel-thumb--placeholder"></div>';
      const priceMarkup = product.price
        ? `<p class="header-panel-price mb-0">R$ ${product.price}</p>`
        : '';

      return `
        <a href="${product.url || '#'}" class="header-panel-item">
          ${imageMarkup}
          <div class="header-panel-content">
            <p class="header-panel-title mb-1">${product.name}</p>
            ${priceMarkup}
          </div>
        </a>
      `;
    }).join('');

    element.innerHTML = `<div class="header-panel-list">${favoritesMarkup}</div>`;
  });
}

document.addEventListener('DOMContentLoaded', updateFavoritesCountBadge);
document.addEventListener('DOMContentLoaded', renderFavoritesList);
document.addEventListener('favorites:updated', updateFavoritesCountBadge);
document.addEventListener('favorites:updated', renderFavoritesList);
window.addEventListener('storage', updateFavoritesCountBadge);
window.addEventListener('storage', renderFavoritesList);

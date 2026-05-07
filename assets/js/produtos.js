const PRODUCT_CATALOG_STORAGE_KEY = 'productCatalog';
let PRODUCT_CATALOG = loadProductCatalog();

function loadProductCatalog() {
  const savedCatalog = localStorage.getItem(PRODUCT_CATALOG_STORAGE_KEY);

  if (!savedCatalog) {
    return {};
  }

  try {
    const parsedCatalog = JSON.parse(savedCatalog);
    return parsedCatalog && typeof parsedCatalog === 'object' ? parsedCatalog : {};
  } catch (error) {
    return {};
  }
}

function saveProductCatalog(catalog) {
  localStorage.setItem(PRODUCT_CATALOG_STORAGE_KEY, JSON.stringify(catalog));
}

function normalizeProductData(product) {
  if (!product || !product.id) {
    return null;
  }

  return {
    id: String(product.id),
    name: product.name || '',
    price: product.price || '',
    image: product.image || '',
    url: product.url || ''
  };
}

function registerProductData(product) {
  const normalizedProduct = normalizeProductData(product);

  if (!normalizedProduct) {
    return null;
  }

  PRODUCT_CATALOG = {
    ...PRODUCT_CATALOG,
    [normalizedProduct.id]: normalizedProduct
  };

  saveProductCatalog(PRODUCT_CATALOG);
  return normalizedProduct;
}

function getProductData(productId) {
  if (!productId) {
    return null;
  }

  return PRODUCT_CATALOG[String(productId)] || null;
}

window.registerProductData = registerProductData;
window.getProductData = getProductData;

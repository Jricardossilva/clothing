const PRODUCT_CATALOG = {
  'camiseta-masculina-preto': {
    id: 'camiseta-masculina-preto',
    name: 'Camiseta Masculina - Preto',
    price: '79,90',
    image: 'assets/img/camiseta-preta.jpg',
    url: 'produto.php'
  }
};

function getProductData(productId) {
  return PRODUCT_CATALOG[productId] || null;
}

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    senha VARCHAR(255),
	data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias(
 	id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR (80),
 	genero ENUM('masculino','feminino'),
    descricao VARCHAR (300)
);

CREATE TABLE produtos(
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT,
    nome VARCHAR(80),
    descricao VARCHAR(300),
    preco DECIMAL(6,2),
    estoque INT,
    situacao INT DEFAULT 1,
    data_cadastro DATETIME DEFAULT,
    url_imagem VARCHAR(255) NULL,
    CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE pagamento(
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    metodo_pagamento ENUM('pix','credito','debito','boleto'),
    status_pagamento ENUM('aprovado','aguardando','em processamento','rejeitado'), 
    codigo_transacao VARCHAR (30),
    data_pagamento DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
    
CREATE TABLE endereco (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT,
    logradouro VARCHAR(200),
	numero INT(5),
	bairro VARCHAR(100),
	cidade VARCHAR(100),
	estado VARCHAR(100),
	cep VARCHAR(8),
	pais VARCHAR(100),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) 
    
);


CREATE TABLE produto_variacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
	produto_id INT,
	cor VARCHAR(10),
	tamanho ENUM('PP','P', 'M', 'G', 'GG','XG'),
	estoque DECIMAL(8,2),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)


);

CREATE TABLE tabela_pedidos (  
    id INT PRIMARY KEY AUTO_INCREMENT,
	cliente_id INT,
    endereco_entrega_id INT,
    valor_total DECIMAL(8,2),
    valor_frete DECIMAL(8,2),
    status_compra ENUM('SEM ESTOQUE','EM ANALISE','AGUARDANDO PAGAMENTO','PREPARANDO PEDIDO','PRODUTO ENVIADO'),
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (endereco_entrega_id) REFERENCES endereco(id)    

    
);

CREATE TABLE produto_imagem (  
    id INT PRIMARY KEY AUTO_INCREMENT,
	produto_id INT,
    url_imagem VARCHAR(255),
    ordem INT(255),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)    

    
);

-- Padrão de insert para cadastrar novos prod
INSERT INTO produtos (nome, descricao, preco, estoque, url_imagem) VALUES 
('Camiseta Feminina 11', 'Camiseta feminina com modelagem confortavel e tecido leve.', 67.90, 15, 'uploads/feminino11.png'),
('Camiseta Feminina 12', 'Camiseta feminina com modelagem confortavel e tecido leve.', 71.90, 15, 'uploads/feminino12.png'),
('Camiseta Feminina 13', 'Camiseta feminina com modelagem confortavel e tecido leve.', 76.90, 15, 'uploads/feminino13.png'),
('Camiseta Feminina 14', 'Camiseta feminina com modelagem confortavel e tecido leve.', 79.90, 15, 'uploads/feminino14.png'),
('Camiseta Feminina 15', 'Camiseta feminina com modelagem confortavel e tecido leve.', 82.90, 15, 'uploads/feminino15.png'),
('Camiseta Feminina 16', 'Camiseta feminina com modelagem confortavel e tecido leve.', 86.90, 15, 'uploads/feminino16.png'),
('Camiseta Feminina 17', 'Camiseta feminina com modelagem confortavel e tecido leve.', 91.90, 15, 'uploads/feminino17.png'),
('Camiseta Feminina 18', 'Camiseta feminina com modelagem confortavel e tecido leve.', 94.90, 15, 'uploads/feminino18.png'),
('Camiseta Feminina 19', 'Camiseta feminina com modelagem confortavel e tecido leve.', 98.90, 15, 'uploads/feminino19.png'),
('Camiseta Feminina 20', 'Camiseta feminina com modelagem confortavel e tecido leve.', 102.90, 15, 'uploads/feminino20.png'),
('Camiseta Masculina 11', 'Camiseta masculina com modelagem confortavel e tecido leve.', 72.90, 15, 'uploads/masculino11.png'),
('Camiseta Masculina 12', 'Camiseta masculina com modelagem confortavel e tecido leve.', 77.90, 15, 'uploads/masculino12.png'),
('Camiseta Masculina 13', 'Camiseta masculina com modelagem confortavel e tecido leve.', 81.90, 15, 'uploads/masculino13.png'),
('Camiseta Masculina 14', 'Camiseta masculina com modelagem confortavel e tecido leve.', 85.90, 15, 'uploads/masculino14.png'),
('Camiseta Masculina 15', 'Camiseta masculina com modelagem confortavel e tecido leve.', 89.90, 15, 'uploads/masculino15.png'),
('Camiseta Masculina 16', 'Camiseta masculina com modelagem confortavel e tecido leve.', 93.90, 15, 'uploads/masculino16.png'),
('Camiseta Masculina 17', 'Camiseta masculina com modelagem confortavel e tecido leve.', 97.90, 15, 'uploads/masculino17.png'),
('Camiseta Masculina 18', 'Camiseta masculina com modelagem confortavel e tecido leve.', 101.90, 15, 'uploads/masculino18.png'),
('Camiseta Masculina 19', 'Camiseta masculina com modelagem confortavel e tecido leve.', 105.90, 15, 'uploads/masculino19.png'),
('Camiseta Masculina 20', 'Camiseta masculina com modelagem confortavel e tecido leve.', 109.90, 15, 'uploads/masculino20.png');

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255),
    dataCadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

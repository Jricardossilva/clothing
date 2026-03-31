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
    url_imagem VARCHAR(50),
    ordem INT(255),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)    

    
);

INSERT INTO produtos (nome, preco, url_imagem) VALUES
('Camiseta Feminina 1', 59.90, 'uploads/feminino1.png'),
('Camiseta Feminina 10', 64.90, 'uploads/feminino10.png'),
('Camiseta Feminina 2', 69.90, 'uploads/feminino2.png'),
('Camiseta Feminina 3', 72.50, 'uploads/feminino3.png'),
('Camiseta Feminina 4', 78.90, 'uploads/feminino4.png'),
('Camiseta Feminina 5', 83.40, 'uploads/feminino5.png'),
('Camiseta Feminina 6', 89.90, 'uploads/feminino6.png'),
('Camiseta Feminina 7', 57.90, 'uploads/feminino7.png'),
('Camiseta Feminina 8', 66.90, 'uploads/feminino8.png'),
('Camiseta Feminina 9', 74.90, 'uploads/feminino9.png'),
('Camiseta Masculina 1', 61.90, 'uploads/masculino1.png'),
('Camiseta Masculina 10', 68.90, 'uploads/masculino10.png'),
('Camiseta Masculina 2', 73.90, 'uploads/masculino2.png'),
('Camiseta Masculina 3', 79.90, 'uploads/masculino3.png'),
('Camiseta Masculina 4', 84.90, 'uploads/masculino4.png'),
('Camiseta Masculina 5', 91.90, 'uploads/masculino5.png'),
('Camiseta Masculina 6', 76.40, 'uploads/masculino6.png'),
('Camiseta Masculina 7', 82.90, 'uploads/masculino7.png'),
('Camiseta Masculina 8', 87.50, 'uploads/masculino8.png'),
('Camiseta Masculina 9', 95.90, 'uploads/masculino9.png');

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255),
    dataCadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
    
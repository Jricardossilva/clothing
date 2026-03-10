CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    senha VARCHAR(255),
	data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
    ordem INT(50),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)    

    
);
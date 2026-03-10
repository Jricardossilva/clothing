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

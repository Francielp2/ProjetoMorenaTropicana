CREATE DATABASE IF NOT EXISTS Morena_Tropicana;
USE Morena_Tropicana;

CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf CHAR(11) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(100) NOT NULL,
    permissao ENUM('ADMIN', 'CLIENTE') DEFAULT 'CLIENTE' NOT NULL
);

CREATE TABLE Administrador (
    id_usuario INT PRIMARY KEY,
    data_contratacao DATE NOT NULL, 
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE Cliente (
    id_usuario INT PRIMARY KEY,
    telefone VARCHAR(11),
    status ENUM('Ativo', 'Inativo', 'Suspenso', 'Cancelado','Pendente') DEFAULT 'Ativo',
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE Endereco (
    id_endereco INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    tipo_endereco ENUM('ENTREGA','COBRANCA','RESIDENCIA','TRABALHO') DEFAULT 'ENTREGA',
    estado CHAR(2),
    cep CHAR(8) NOT NULL,
    bairro VARCHAR(100),
    rua VARCHAR(100),
    numero VARCHAR(10),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_usuario)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE Produto (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    categoria VARCHAR(50),
    preco DECIMAL(10,2) NOT NULL,
    tamanhos_disponiveis VARCHAR(50),
    cores_disponiveis VARCHAR(50),
    imagens VARCHAR(255)
);

CREATE TABLE Administrador_Produto(
	id_adm INT,
    id_produto INT,
    PRIMARY KEY (id_adm, id_produto),
    FOREIGN KEY(id_adm) REFERENCES Administrador(id_usuario)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	FOREIGN KEY(id_produto) REFERENCES Produto(id_produto)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE Estoque (
    id_estoque INT AUTO_INCREMENT PRIMARY KEY,
    id_produto INT NOT NULL,
    quantidade INT DEFAULT 0,
    modelo_produto VARCHAR(50),
    data_cadastro DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (id_produto) REFERENCES Produto(id_produto)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE Pedido (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    status_pedido ENUM('FINALIZADO','CANCELADO','ENTREGUE','PENDENTE') DEFAULT 'PENDENTE',
    valor_total DECIMAL(10,2),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_usuario)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE Produto_Pedido (
    id_pedido INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT DEFAULT 1,
    preco_unitario DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id_pedido, id_produto),
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_produto) REFERENCES Produto(id_produto)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE Pagamento (
    id_pagamento INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    data_pagamento DATETIME DEFAULT CURRENT_TIMESTAMP,
    status_pagamento ENUM('CONFIRMADO','PENDENTE') DEFAULT 'PENDENTE',
    forma_pagamento ENUM('Cartão', 'Pix', 'Boleto'),
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE Administrador_Pedido (
    id_adm INT,
    id_pedido INT,
    PRIMARY KEY (id_adm , id_pedido),
    FOREIGN KEY (id_adm) REFERENCES Administrador (id_usuario)
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (id_pedido) REFERENCES Pedido (id_pedido)
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);
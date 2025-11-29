-- Script para criar um administrador no banco de dados
-- Execute este script no seu banco de dados MySQL

USE Morena_Tropicana;

-- Insere o administrador na tabela Usuarios
-- IMPORTANTE: Altere os valores abaixo antes de executar!
INSERT INTO Usuarios (nome, cpf, email, senha, permissao) 
VALUES (
    'Administrador',                    -- Nome do administrador
    '00000000000',                      -- CPF (11 dígitos)
    'admin@morenatropicana.com',        -- Email
    '$2b$12$QRGRFViKMJERzGFz9lVlYOh8OW.TsF1H.XNSkaG4yNo2IzHb3TroW',  -- Senha: "Administrador"
    'ADMIN'                              -- Permissão
);

-- Pega o ID do usuário criado e insere na tabela Administrador
INSERT INTO Administrador (id_usuario, data_contratacao)
SELECT id_usuario, CURDATE()
FROM Usuarios
WHERE email = 'admin@morenatropicana.com' AND permissao = 'ADMIN'
LIMIT 1;

-- IMPORTANTE: 
-- 1. Altere o email e senha acima antes de executar
-- 2. A senha acima é "password" - ALTERE IMEDIATAMENTE após o primeiro login!
-- 3. Para gerar uma nova senha hash, use: password_hash('sua_senha', PASSWORD_DEFAULT) no PHP


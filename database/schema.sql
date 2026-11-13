-- Schema do banco de dados do projeto Diário Financeiro
-- Execute este script no MySQL para criar o banco do zero.
--
-- Na hospedagem (InfinityFree) o banco já existe: apague as duas
-- primeiras linhas (CREATE DATABASE e USE) antes de importar.

CREATE DATABASE IF NOT EXISTS diario_de_financas;
USE diario_de_financas;

-- 1. Usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Categorias (lista fixa, igual para todos os usuários)
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
);

-- 3. Transações (de um usuário e, opcionalmente, de uma categoria)
CREATE TABLE transacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    tipo ENUM('entrada', 'saida', 'investimento') NOT NULL,
    categoria_id INT NULL,
    data_transacao DATE NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    -- se uma categoria for removida, a transação continua, sem categoria
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- 4. Dados iniciais das categorias
INSERT INTO categorias (nome) VALUES
('Alimentação'), ('Moradia'), ('Contas e assinaturas'), ('Transporte'),
('Saúde'), ('Educação'), ('Lazer'), ('Compras'), ('Pet'), ('Viagem'),
('Salário'), ('Freelance'), ('Rendimentos'),
('Poupança'), ('Renda fixa'), ('Renda variável'), ('Reserva de emergência'),
('Outros');
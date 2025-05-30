<?php
require_once 'database.php';

// SQL to create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS categorias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS produtos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        descricao TEXT,
        preco DECIMAL(10,2) NOT NULL,
        imagem VARCHAR(255),
        estoque INT DEFAULT 0,
        categoria_id INT NOT NULL,
        FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS vendas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome_cliente VARCHAR(255) NOT NULL,
        email_cliente VARCHAR(255) NOT NULL,
        endereco_cliente TEXT NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS vendas_itens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        venda_id INT NOT NULL,
        produto_id INT NOT NULL,
        quantidade INT NOT NULL,
        preco_unitario DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
    )",
    
    "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

// Create tables
foreach ($tables as $sql) {
    try {
        $pdo->exec($sql);
    } catch (PDOException $e) {
        die("Error creating tables: " . $e->getMessage());
    }
}

// Create default admin user if none exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios");
$stmt->execute();
if ($stmt->fetchColumn() == 0) {
    $senha_hash = password_hash("admin123", PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->execute(["Administrador", "admin@loja.com", $senha_hash]);
    echo "Usuário admin criado com sucesso! Email: admin@loja.com, Senha: admin123";
}

echo "Setup concluído com sucesso!";
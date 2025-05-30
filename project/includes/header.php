<?php
session_start();
require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja Virtual</title>
    <link rel="stylesheet" href="http://localhost/project/assets/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="http://localhost/project/index.php">Loja Virtual</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="http://localhost/project/index.php">Início</a></li>
                    <?php 
                    // Get categories for menu
                    $stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY nome");
                    $stmt->execute();
                    $categorias = $stmt->fetchAll();
                    
                    foreach($categorias as $categoria): 
                    ?>
                    <li>
                        <a href="http://localhost/project/categoria.php?id=<?= $categoria['id'] ?>"><?= $categoria['nome'] ?></a>
                    </li>
                    <?php endforeach; ?>
                    <li><a href="http://localhost/project/carrinho.php" class="cart-link">Carrinho 
                    <?php 
                    if(isset($_SESSION['carrinho']) && count($_SESSION['carrinho']) > 0) {
                        $total_itens = array_sum(array_column($_SESSION['carrinho'], 'quantidade'));
                        echo "<span class=\"cart-count\">$total_itens</span>";
                    }
                    ?>
                    </a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main-content">
        <div class="container">
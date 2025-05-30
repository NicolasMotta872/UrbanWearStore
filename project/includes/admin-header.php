<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['usuario_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header('Location: /project/admin/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Loja Virtual</title>
    <link rel="stylesheet" href="http://localhost/project/assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php if(isset($_SESSION['usuario_id'])): ?>
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <a href="http://localhost/project/admin/dashboard.php">Loja Virtual</a>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="http://localhost/project/admin/dashboard.php">Dashboard</a></li>
                    <li><a href="http://localhost/project/admin/categorias/index.php">Categorias</a></li>
                    <li><a href="http://localhost/project/admin/produtos/index.php">Produtos</a></li>
                    <li><a href="http://localhost/project/admin/vendas/index.php">Vendas</a></li>
                    <li><a href="http://localhost/project/admin/logout.php">Sair</a></li>
                </ul>
            </nav>
        </aside>
        <?php endif; ?>
        
        <main class="admin-content">
            <?php if(isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?>">
                <?= $_SESSION['flash_message'] ?>
            </div>
            <?php 
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_type']);
            endif; 
            ?>
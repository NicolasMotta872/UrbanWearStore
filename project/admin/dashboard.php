<?php
require_once '../includes/admin-header.php';

// Estatísticas do dashboard
// 1. Total de vendas
$stmt = $pdo->query("SELECT COUNT(*) as total, SUM(total) as valor_total FROM vendas");
$vendas = $stmt->fetch();

// 2. Total de produtos
$stmt = $pdo->query("SELECT COUNT(*) FROM produtos");
$total_produtos = $stmt->fetchColumn();

// 3. Total de categorias
$stmt = $pdo->query("SELECT COUNT(*) FROM categorias");
$total_categorias = $stmt->fetchColumn();
?>
<div class="admin-header">
    <h1 class="admin-title">Dashboard</h1>
</div>

<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-icon">💰</div>
        <div class="admin-card-title">Total de Vendas</div>
        <div class="admin-card-value"><?= $vendas['total'] ?></div>
    </div>
    
    <div class="admin-card">
        <div class="admin-card-icon">💵</div>
        <div class="admin-card-title">Valor Total de Vendas</div>
        <div class="admin-card-value">R$ <?= number_format($vendas['valor_total'] ?? 0, 2, ',', '.') ?></div>
    </div>
    
    <div class="admin-card">
        <div class="admin-card-icon">📦</div>
        <div class="admin-card-title">Total de Produtos</div>
        <div class="admin-card-value"><?= $total_produtos ?></div>
    </div>
    
    <div class="admin-card">
        <div class="admin-card-icon">🏷️</div>
        <div class="admin-card-title">Total de Categorias</div>
        <div class="admin-card-value"><?= $total_categorias ?></div>
    </div>
</div>


<?php require_once '../includes/admin-footer.php'; ?>
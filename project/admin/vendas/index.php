<?php
require_once '../../includes/admin-header.php';

// Remover filtros e paginação
$where = "1=1";
$params = [];

// Buscar todas as vendas
$query = "
    SELECT v.*, COUNT(vi.id) as total_itens 
    FROM vendas v
    LEFT JOIN vendas_itens vi ON v.id = vi.venda_id
    WHERE $where
    GROUP BY v.id
    ORDER BY v.data_venda DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$vendas = $stmt->fetchAll();

// Contagem total de registros e valor total
$total_registros = count($vendas);
$stmt = $pdo->prepare("SELECT SUM(total) FROM vendas WHERE $where");
$stmt->execute($params);
$valor_total = $stmt->fetchColumn();
?>

<div class="admin-header">
    <h1 class="admin-title">Gerenciar Vendas</h1>
</div>

<!-- Filtros removidos -->

<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>E-mail</th>
                <th>Data</th>
                <th>Total</th>
                <th>Itens</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($vendas) > 0): ?>
                <?php foreach ($vendas as $venda): ?>
                    <tr>
                        <td><?= $venda['id'] ?></td>
                        <td><?= $venda['nome_cliente'] ?></td>
                        <td><?= $venda['email_cliente'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?></td>
                        <td>R$ <?= number_format($venda['total'], 2, ',', '.') ?></td>
                        <td><?= $venda['total_itens'] ?></td>
                        <td>
                            <a href="http://localhost/project/admin/vendas/ver.php?id=<?= $venda['id'] ?>" class="btn btn-primary btn-sm">Ver Detalhes</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Nenhuma venda encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="admin-summary">
        <p>Total de Vendas: <?= $total_registros ?></p>
        <?php if ($total_registros > 0): ?>
            <p>Valor Total: R$ <?= number_format($valor_total, 2, ',', '.') ?></p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/admin-footer.php'; ?>
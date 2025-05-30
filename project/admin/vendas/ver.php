<?php
require_once '../../includes/admin-header.php';

// Verificar se o ID da venda foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "ID de venda inválido";
    $_SESSION['flash_type'] = "danger";
    header('Location: /project/admin/index.php');
    exit;
}

$venda_id = $_GET['id'];

// Buscar informações da venda
$stmt = $pdo->prepare("SELECT * FROM vendas WHERE id = ?");
$stmt->execute([$venda_id]);
$venda = $stmt->fetch();

// Se a venda não existe, redirecionar
if (!$venda) {
    $_SESSION['flash_message'] = "Venda não encontrada";
    $_SESSION['flash_type'] = "danger";
    header('Location: /project/admin/index.php');
    exit;
}

// Buscar itens da venda
$stmt = $pdo->prepare("
    SELECT vi.*, p.nome as produto_nome, p.imagem as produto_imagem
    FROM vendas_itens vi
    JOIN produtos p ON vi.produto_id = p.id
    WHERE vi.venda_id = ?
");
$stmt->execute([$venda_id]);
$itens = $stmt->fetchAll();
?>

<div class="admin-header">
    <h1 class="admin-title">Detalhes da Venda #<?= $venda_id ?></h1>
    <a href="http://localhost/project/admin/vendas/index.php" class="btn btn-secondary">Voltar</a>
</div>

<div class="admin-card">
    <div class="admin-section">
        <h2 class="section-title">Informações da Venda</h2>
        
        <div class="info-group">
            <div class="info-row">
                <div class="info-label">Cliente:</div>
                <div class="info-value"><?= $venda['nome_cliente'] ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">E-mail:</div>
                <div class="info-value"><?= $venda['email_cliente'] ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Endereço:</div>
                <div class="info-value"><?= $venda['endereco_cliente'] ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Data:</div>
                <div class="info-value"><?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Total:</div>
                <div class="info-value">R$ <?= number_format($venda['total'], 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
    
    <div class="admin-section">
        <h2 class="section-title">Itens do Pedido</h2>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço Unitário</th>
                    <th>Quantidade</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($itens) > 0): ?>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td>
                                <div class="produto-info">
                                    <?php if (!empty($item['produto_imagem'])): ?>
                                        <img src="<?= $item['produto_imagem'] ?>" alt="<?= $item['produto_nome'] ?>" class="produto-thumb">
                                    <?php endif; ?>
                                    <span><?= $item['produto_nome'] ?></span>
                                </div>
                            </td>
                            <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                            <td><?= $item['quantidade'] ?></td>
                            <td>R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">Nenhum item encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td><strong>R$ <?= number_format($venda['total'], 2, ',', '.') ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php require_once '../../includes/admin-footer.php'; ?>
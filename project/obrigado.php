<?php
require_once 'includes/header.php';

// Verificar se o ID do pedido foi fornecido
if (!isset($_GET['pedido']) || !is_numeric($_GET['pedido'])) {
    header('Location: /project/index.php');
    exit;
}

$pedido_id = $_GET['pedido'];

// Buscar informações do pedido
$stmt = $pdo->prepare("SELECT * FROM vendas WHERE id = ?");
$stmt->execute([$pedido_id]);
$pedido = $stmt->fetch();

// Se o pedido não existe, redirecionar para a página inicial
if (!$pedido) {
    header('Location: /project/index.php');
    exit;
}
?>

<div class="container">
    <div class="thank-you-container">
        <div class="thank-you-icon">✓</div>
        
        <h1>Obrigado pela sua compra!</h1>
        
        <p>Seu pedido #<?= $pedido_id ?> foi realizado com sucesso.</p>
        
        <div class="order-details">
            <p>Um e-mail de confirmação foi enviado para <strong><?= $pedido['email_cliente'] ?></strong>.</p>
            <p>Data do pedido: <?= date('d/m/Y H:i', strtotime($pedido['data_venda'])) ?></p>
            <p>Total: R$ <?= number_format($pedido['total'], 2, ',', '.') ?></p>
        </div>
        
        <div class="thank-you-actions">
            <a href="http://localhost/project/index.php" class="btn btn-primary">Continuar Comprando</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<?php
require_once 'includes/header.php';

// Verificar se o ID da categoria foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /project/index.php');
    exit;
}

$categoria_id = $_GET['id'];

// Buscar informações da categoria
$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
$stmt->execute([$categoria_id]);
$categoria = $stmt->fetch();

// Se a categoria não existe, redirecionar para a página inicial
if (!$categoria) {
    header('Location: /project/index.php');
    exit;
}

// Buscar produtos da categoria
$stmt = $pdo->prepare("
    SELECT * FROM produtos 
    WHERE categoria_id = ? AND estoque > 0
    ORDER BY nome
");
$stmt->execute([$categoria_id]);
$produtos = $stmt->fetchAll();
?>

<div class="breadcrumbs">
    <div class="container">
        <a href="http://localhost/project/index.php">Início</a> &gt;
        <span><?= $categoria['nome'] ?></span>
    </div>
</div>

<div class="container">
    <div class="category-header">
        <h1 class="category-title"><?= $categoria['nome'] ?></h1>
    </div>
    
    <div class="products-grid">
        <?php if (count($produtos) > 0): ?>
            <?php foreach ($produtos as $produto): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($produto['imagem'])): ?>
                            <img src="http://localhost/project<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>">
                        <?php else: ?>
                            <img src="http://localhost/project/assets/img/no-image.jpg" alt="Imagem não disponível">
                        <?php endif; ?>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?= $produto['nome'] ?></h3>
                        <div class="product-price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
                        <div class="product-actions">
                            <a href="http://localhost/project/produto.php?id=<?= $produto['id'] ?>" class="btn btn-primary">Ver Detalhes</a>
                            <form action="http://localhost/project/carrinho.php" method="post" class="add-to-cart-form">
                                <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                                <input type="hidden" name="quantidade" value="1">
                                <input type="hidden" name="acao" value="adicionar">
                                <button type="submit" class="btn btn-secondary">Adicionar ao Carrinho</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-products">Nenhum produto disponível nesta categoria no momento.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
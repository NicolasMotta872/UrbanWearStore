<?php
require_once 'includes/header.php';

// Buscar produtos em destaque (últimos 8 adicionados)
$stmt = $pdo->prepare("
    SELECT p.*, c.nome as categoria_nome 
    FROM produtos p
    JOIN categorias c ON p.categoria_id = c.id
    WHERE p.estoque > 0
    ORDER BY p.id DESC
    LIMIT 8
");
$stmt->execute();
$produtos = $stmt->fetchAll();
?>

<div class="hero-section">
    <div class="hero-content">
        <h1>StoreScobars</h1>
        <p>A melhor loja de roupas online</p>
        <a href="#produtos-destaque" class="btn btn-secondary">Ver Produtos</a>
    </div>
</div>

<section id="produtos-destaque" class="section">
    <div class="section-header">
        <h2>Produtos em Destaque</h2>
        <p>Confira nossos produtos mais recentes</p>
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
                        <div class="product-category"><?= $produto['categoria_nome'] ?></div>
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
            <p>Nenhum produto disponível no momento.</p>
        <?php endif; ?>
    </div>
</section>

<section class="categories-section">
    <div class="section-header">
        <h2>Nossas Categorias</h2>
        <p>Navegue por categoria para encontrar o que você procura</p>
    </div>
    
    <div class="categories-grid">
        <?php foreach ($categorias as $categoria): ?>
            <a href="http://localhost/project/categoria.php?id=<?= $categoria['id'] ?>" class="category-card">
                <div class="category-title"><?= $categoria['nome'] ?></div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
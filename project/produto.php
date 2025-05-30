<?php
require_once 'includes/header.php';

// Verificar se o ID do produto foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /project/index.php');
    exit;
}

$produto_id = $_GET['id'];

// Buscar informações do produto
$stmt = $pdo->prepare("
    SELECT p.*, c.nome as categoria_nome 
    FROM produtos p
    JOIN categorias c ON p.categoria_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$produto_id]);
$produto = $stmt->fetch();

// Se o produto não existe, redirecionar para a página inicial
if (!$produto) {
    header('Location: /project/index.php');
    exit;
}

// Processar adição ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
    $quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;
    
    // Validar quantidade
    if ($quantidade <= 0) {
        $quantidade = 1;
    }
    
    // Verificar estoque
    if ($quantidade > $produto['estoque']) {
        $erro = "Desculpe, temos apenas {$produto['estoque']} unidades em estoque.";
    } else {
        // Inicializar o carrinho se não existir
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
        
        // Verificar se o produto já está no carrinho
        if (isset($_SESSION['carrinho'][$produto_id])) {
            // Atualizar quantidade
            $_SESSION['carrinho'][$produto_id]['quantidade'] += $quantidade;
            
            // Verificar se não excede o estoque
            if ($_SESSION['carrinho'][$produto_id]['quantidade'] > $produto['estoque']) {
                $_SESSION['carrinho'][$produto_id]['quantidade'] = $produto['estoque'];
                $mensagem = "Quantidade ajustada para o máximo disponível em estoque.";
            } else {
                $mensagem = "Quantidade atualizada no carrinho!";
            }
        } else {
            // Adicionar novo item ao carrinho
            $_SESSION['carrinho'][$produto_id] = [
                'id' => $produto_id,
                'nome' => $produto['nome'],
                'preco' => $produto['preco'],
                'quantidade' => $quantidade,
                'imagem' => $produto['imagem']
            ];
            $mensagem = "Produto adicionado ao carrinho!";
        }
        
        // Redirecionar para o carrinho
        header("Location: /project/carrinho.php?mensagem=" . urlencode($mensagem));
        exit;
    }
}
?>

<div class="breadcrumbs">
    <div class="container">
        <a href="http://localhost/project/index.php">Início</a> &gt;
        <a href="http://localhost/project/categoria.php?id=<?= $produto['categoria_id'] ?>"><?= $produto['categoria_nome'] ?></a> &gt;
        <span><?= $produto['nome'] ?></span>
    </div>
</div>

<div class="container">
    <?php if (isset($erro)): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php endif; ?>
    
    <div class="product-container">
        <div class="product-gallery">
            <?php if (!empty($produto['imagem'])): ?>
                <img src="http://localhost/project<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>" class="product-main-image">
            <?php else: ?>
                <img src="http://localhost/project/assets/img/no-image.jpg" alt="Imagem não disponível" class="product-main-image">
            <?php endif; ?>
        </div>
        
        <div class="product-info">
            <h1><?= $produto['nome'] ?></h1>
            
            <div class="product-meta">
                <div class="product-category">Categoria: <?= $produto['categoria_nome'] ?></div>
                <div class="product-price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
                <div class="product-stock">
                    <?php if ($produto['estoque'] > 0): ?>
                        <span class="in-stock">Em estoque: <?= $produto['estoque'] ?> unidades</span>
                    <?php else: ?>
                        <span class="out-of-stock">Produto indisponível</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="product-description">
                <?= nl2br($produto['descricao']) ?>
            </div>
            
            <?php if ($produto['estoque'] > 0): ?>
                <form action="" method="post">
                    <div class="quantity-selector">
                        <label for="quantidade">Quantidade:</label>
                        <button type="button" class="quantity-btn decrease">-</button>
                        <input type="number" id="quantidade" name="quantidade" value="1" min="1" max="<?= $produto['estoque'] ?>">
                        <button type="button" class="quantity-btn increase">+</button>
                    </div>
                    
                    <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                    <input type="hidden" name="acao" value="adicionar">
                    <button type="submit" class="btn btn-primary btn-block">Adicionar ao Carrinho</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
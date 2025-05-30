<?php
require_once 'includes/header.php';

// Inicializar carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Processar ações do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    
    // Adicionar produto ao carrinho
    if ($acao === 'adicionar' && isset($_POST['produto_id']) && isset($_POST['quantidade'])) {
        $produto_id = (int)$_POST['produto_id'];
        $quantidade = (int)$_POST['quantidade'];
        
        // Validar quantidade
        if ($quantidade <= 0) {
            $quantidade = 1;
        }
        
        // Verificar estoque
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ? AND estoque >= ?");
        $stmt->execute([$produto_id, $quantidade]);
        $produto = $stmt->fetch();
        
        if ($produto) {
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
        } else {
            $erro = "Produto não disponível ou quantidade excede o estoque.";
        }
    }
    
    // Atualizar quantidade
    else if ($acao === 'atualizar' && isset($_POST['quantidades'])) {
        $quantidades = $_POST['quantidades'];
        
        foreach ($quantidades as $produto_id => $quantidade) {
            $produto_id = (int)$produto_id;
            $quantidade = (int)$quantidade;
            
            // Verificar se o produto existe no carrinho
            if (isset($_SESSION['carrinho'][$produto_id])) {
                // Remover produto se quantidade for zero
                if ($quantidade <= 0) {
                    unset($_SESSION['carrinho'][$produto_id]);
                    continue;
                }
                
                // Verificar estoque
                $stmt = $pdo->prepare("SELECT estoque FROM produtos WHERE id = ?");
                $stmt->execute([$produto_id]);
                $estoque = $stmt->fetchColumn();
                
                // Ajustar quantidade ao estoque disponível
                if ($quantidade > $estoque) {
                    $_SESSION['carrinho'][$produto_id]['quantidade'] = $estoque;
                    $mensagem = "Quantidade ajustada para o máximo disponível em estoque.";
                } else {
                    $_SESSION['carrinho'][$produto_id]['quantidade'] = $quantidade;
                }
            }
        }
        
        $mensagem = "Carrinho atualizado com sucesso!";
    }
    
    // Remover produto
    else if ($acao === 'remover' && isset($_POST['produto_id'])) {
        $produto_id = (int)$_POST['produto_id'];
        
        if (isset($_SESSION['carrinho'][$produto_id])) {
            unset($_SESSION['carrinho'][$produto_id]);
            $mensagem = "Produto removido do carrinho!";
        }
    }
    
    // Limpar carrinho
    else if ($acao === 'limpar') {
        $_SESSION['carrinho'] = [];
        $mensagem = "Carrinho foi esvaziado!";
    }
}

// Calcular total do carrinho
$total_carrinho = 0;
foreach ($_SESSION['carrinho'] as $item) {
    $total_carrinho += $item['preco'] * $item['quantidade'];
}

// Exibir mensagem de GET (redirecionamento)
if (isset($_GET['mensagem'])) {
    $mensagem = $_GET['mensagem'];
}
?>

<div class="container">
    <h1 class="page-title">Meu Carrinho</h1>
    
    <?php if (isset($mensagem)): ?>
        <div class="alert alert-success"><?= $mensagem ?></div>
    <?php endif; ?>
    
    <?php if (isset($erro)): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php endif; ?>
    
    <?php if (empty($_SESSION['carrinho'])): ?>
        <div class="empty-cart">
            <p>Seu carrinho está vazio.</p>
            <a href="http://localhost/project/index.php" class="btn btn-primary">Continuar Comprando</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <div class="cart-items">
                <form action="" method="post" id="cart-update-form">
                    <input type="hidden" name="acao" value="atualizar">
                    
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th>Quantidade</th>
                                <th>Subtotal</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['carrinho'] as $produto_id => $item): ?>
                                <tr>
                                    <td data-label="Produto" class="cart-product">
                                        <div class="cart-product-image">
                                            <?php if (!empty($item['imagem'])): ?>
                                                <img src="http://localhost/project<?= $item['imagem'] ?>" alt="<?= $item['nome'] ?>">
                                            <?php else: ?>
                                                <img src="http://localhost/project/assets/img/no-image.jpg" alt="Imagem não disponível">
                                            <?php endif; ?>
                                        </div>
                                        <div class="cart-product-info">
                                            <h3><?= $item['nome'] ?></h3>
                                        </div>
                                    </td>
                                    <td data-label="Preço">R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                                    <td data-label="Quantidade">
                                        <div class="quantity-selector">
                                            <button type="button" class="quantity-btn decrease">-</button>
                                            <input type="number" name="quantidades[<?= $produto_id ?>]" value="<?= $item['quantidade'] ?>" min="1">
                                            <button type="button" class="quantity-btn increase">+</button>
                                        </div>
                                    </td>
                                    <td data-label="Subtotal">
                                        R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?>
                                    </td>
                                    <td data-label="Ações">
                                        <form action="" method="post" class="remove-form">
                                            <input type="hidden" name="acao" value="remover">
                                            <input type="hidden" name="produto_id" value="<?= $produto_id ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="cart-actions">
                        <button type="submit" class="btn btn-secondary">Atualizar Carrinho</button>
                        <a href="http://localhost/project/index.php" class="btn btn-primary">Continuar Comprando</a>
                    </div>
                </form>
            </div>
            
            <div class="cart-summary">
                <h3>Resumo do Pedido</h3>
                
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>R$ <?= number_format($total_carrinho, 2, ',', '.') ?></span>
                </div>
                
                <div class="summary-row">
                    <span>Frete</span>
                    <span>Grátis</span>
                </div>
                
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span>R$ <?= number_format($total_carrinho, 2, ',', '.') ?></span>
                </div>
                
                <a href="http://localhost/project/finalizar.php" class="btn btn-primary btn-block">Finalizar Compra</a>
                
                <form action="" method="post" class="clear-cart-form">
                    <input type="hidden" name="acao" value="limpar">
                    <button type="submit" class="btn btn-secondary btn-block">Limpar Carrinho</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
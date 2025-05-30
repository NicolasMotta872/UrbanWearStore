<?php
require_once 'includes/header.php';

// Verificar se o carrinho está vazio
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header('Location: /project/carrinho.php');
    exit;
}

// Calcular total do carrinho
$total_carrinho = 0;
foreach ($_SESSION['carrinho'] as $item) {
    $total_carrinho += $item['preco'] * $item['quantidade'];
}

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar dados do formulário
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $endereco = isset($_POST['endereco']) ? trim($_POST['endereco']) : '';
    $cidade = isset($_POST['cidade']) ? trim($_POST['cidade']) : '';
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';
    $cep = isset($_POST['cep']) ? trim($_POST['cep']) : '';
    
    $erro = false;
    $mensagens = [];
    
    // Validações básicas
    if (empty($nome)) {
        $erro = true;
        $mensagens[] = "O nome é obrigatório";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = true;
        $mensagens[] = "E-mail inválido";
    }
    
    if (empty($endereco)) {
        $erro = true;
        $mensagens[] = "O endereço é obrigatório";
    }
    
    if (empty($cidade)) {
        $erro = true;
        $mensagens[] = "A cidade é obrigatória";
    }
    
    if (empty($estado)) {
        $erro = true;
        $mensagens[] = "O estado é obrigatório";
    }
    
    if (empty($cep)) {
        $erro = true;
        $mensagens[] = "O CEP é obrigatório";
    }
    
    // Se não há erros, processar a venda
    if (!$erro) {
        try {
            // Iniciar transação
            $pdo->beginTransaction();
            
            // Criar registro de venda
            $endereco_completo = $endereco . ', ' . $cidade . ', ' . $estado . ' - ' . $cep;
            
            $stmt = $pdo->prepare("
                INSERT INTO vendas (nome_cliente, email_cliente, endereco_cliente, total, data_venda)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$nome, $email, $endereco_completo, $total_carrinho]);
            
            $venda_id = $pdo->lastInsertId();
            
            // Registrar itens da venda
            $stmt = $pdo->prepare("
                INSERT INTO vendas_itens (venda_id, produto_id, quantidade, preco_unitario)
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($_SESSION['carrinho'] as $produto_id => $item) {
                $stmt->execute([
                    $venda_id,
                    $produto_id,
                    $item['quantidade'],
                    $item['preco']
                ]);
                
                // Atualizar estoque
                $stmt_estoque = $pdo->prepare("
                    UPDATE produtos 
                    SET estoque = estoque - ? 
                    WHERE id = ?
                ");
                $stmt_estoque->execute([$item['quantidade'], $produto_id]);
            }
            
            // Finalizar transação
            $pdo->commit();
            
            // Limpar carrinho
            $_SESSION['carrinho'] = [];
            
            // Redirecionar para página de agradecimento
            header("Location: /project/obrigado.php?pedido=" . $venda_id);
            exit;
            
        } catch (PDOException $e) {
            // Reverter em caso de erro
            $pdo->rollBack();
            $erro = true;
            $mensagens[] = "Erro ao processar pedido: " . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <h1 class="page-title">Finalizar Compra</h1>
    
    <?php if (isset($mensagens) && !empty($mensagens)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($mensagens as $mensagem): ?>
                    <li><?= $mensagem ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="checkout-container">
        <div class="checkout-form">
            <h2>Informações de Entrega</h2>
            
            <form action="" method="post" id="checkout-form">
                <div class="form-group">
                    <label for="nome" class="form-label">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="<?= isset($nome) ? $nome : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">E-mail *</label>
                    <input type="email" id="email" name="email" class="form-control" required value="<?= isset($email) ? $email : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="endereco" class="form-label">Endereço *</label>
                    <input type="text" id="endereco" name="endereco" class="form-control" required value="<?= isset($endereco) ? $endereco : '' ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="cidade" class="form-label">Cidade *</label>
                        <input type="text" id="cidade" name="cidade" class="form-control" required value="<?= isset($cidade) ? $cidade : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="estado" class="form-label">Estado *</label>
                        <input type="text" id="estado" name="estado" class="form-control" required value="<?= isset($estado) ? $estado : '' ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="cep" class="form-label">CEP *</label>
                    <input type="text" id="cep" name="cep" class="form-control" required value="<?= isset($cep) ? $cep : '' ?>">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Concluir Pedido</button>
            </form>
        </div>
        
        <div class="order-summary">
            <h2>Resumo do Pedido</h2>
            
            <div class="order-items">
                <?php foreach ($_SESSION['carrinho'] as $item): ?>
                    <div class="order-item">
                        <div class="order-item-details">
                            <div class="order-item-name"><?= $item['nome'] ?></div>
                            <div class="order-item-price">
                                <?= $item['quantidade'] ?> x R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                            </div>
                        </div>
                        <div class="order-item-total">
                            R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="order-total">
                <div class="total-label">Total</div>
                <div class="total-value">R$ <?= number_format($total_carrinho, 2, ',', '.') ?></div>
            </div>
            
            <a href="http://localhost/project/carrinho.php" class="btn btn-secondary btn-block">Voltar ao Carrinho</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
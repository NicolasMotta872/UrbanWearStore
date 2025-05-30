<?php
require_once '../../includes/admin-header.php';

// Verificar se o ID do produto foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "ID de produto inválido";
    $_SESSION['flash_type'] = "danger";
    header('Location: /project/admin/index.php');
    exit;
}

$produto_id = $_GET['id'];

// Buscar produto
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$produto_id]);
$produto = $stmt->fetch();

// Se o produto não existe, redirecionar
if (!$produto) {
    $_SESSION['flash_message'] = "Produto não encontrado";
    $_SESSION['flash_type'] = "danger";
    header('Location: /project/admin/index.php');
    exit;
}

// Buscar categorias
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
$categorias = $stmt->fetchAll();

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $preco = isset($_POST['preco']) ? str_replace(',', '.', $_POST['preco']) : 0;
    $estoque = isset($_POST['estoque']) ? (int)$_POST['estoque'] : 0;
    $categoria_id = isset($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : 0;
    
    $erro = false;
    $mensagens = [];
    
    // Validações
    if (empty($nome)) {
        $erro = true;
        $mensagens[] = "O nome do produto é obrigatório";
    }
    
    if (!is_numeric($preco) || $preco <= 0) {
        $erro = true;
        $mensagens[] = "O preço deve ser um valor positivo";
    }
    
    if ($estoque < 0) {
        $erro = true;
        $mensagens[] = "O estoque não pode ser negativo";
    }
    
    if ($categoria_id <= 0) {
        $erro = true;
        $mensagens[] = "Selecione uma categoria válida";
    }
    
    // Manter a imagem atual se não houver upload
    $imagem = $produto['imagem'];
    
    // Processar upload de imagem se houver
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $arquivo_tmp = $_FILES['imagem']['tmp_name'];
        $nome_arquivo = $_FILES['imagem']['name'];
        $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($extensao, $extensoes_permitidas)) {
            $erro = true;
            $mensagens[] = "Extensão de arquivo não permitida. Use: " . implode(', ', $extensoes_permitidas);
        } else {
            // Criar diretório de uploads se não existir
            $diretorio_uploads = __DIR__ . '/../../uploads';
            if (!file_exists($diretorio_uploads)) {
                mkdir($diretorio_uploads, 0777, true);
            }
            
            // Gerar nome único para o arquivo
            $nome_unico = md5(uniqid(rand(), true)) . '.' . $extensao;
            $caminho_arquivo = $diretorio_uploads . '/' . $nome_unico;
            
            // Mover o arquivo
            if (move_uploaded_file($arquivo_tmp, $caminho_arquivo)) {
                $imagem = '/uploads/' . $nome_unico;
                
                // Remover imagem antiga se existir
                if (!empty($produto['imagem']) && file_exists(__DIR__ . '/../../' . $produto['imagem'])) {
                    unlink(__DIR__ . '/../../' . $produto['imagem']);
                }
            } else {
                $erro = true;
                $mensagens[] = "Erro ao fazer upload da imagem";
            }
        }
    }
    
    // Se não há erros, atualizar produto
    if (!$erro) {
        try {
            $stmt = $pdo->prepare("
                UPDATE produtos 
                SET nome = ?, descricao = ?, preco = ?, imagem = ?, estoque = ?, categoria_id = ?
                WHERE id = ?
            ");
            $stmt->execute([$nome, $descricao, $preco, $imagem, $estoque, $categoria_id, $produto_id]);
            
            $_SESSION['flash_message'] = "Produto atualizado com sucesso!";
            $_SESSION['flash_type'] = "success";
            
            header("Location: /project/admin/produtos/index.php");
            exit;
        } catch (PDOException $e) {
            $erro = true;
            $mensagens[] = "Erro ao atualizar produto: " . $e->getMessage();
        }
    }
}
?>

<div class="admin-header">
    <h1 class="admin-title">Editar Produto</h1>
    <a href="http://localhost/project/admin/produtos/index.php" class="btn btn-secondary">Voltar</a>
</div>

<?php if (isset($mensagens) && !empty($mensagens)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($mensagens as $mensagem): ?>
                <li><?= $mensagem ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="admin-form">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome" class="form-label">Nome do Produto *</label>
            <input type="text" id="nome" name="nome" class="form-control" required value="<?= $produto['nome'] ?>">
        </div>
        
        <div class="form-group">
            <label for="categoria_id" class="form-label">Categoria *</label>
            <select id="categoria_id" name="categoria_id" class="form-control" required>
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria['id'] ?>" <?= $produto['categoria_id'] == $categoria['id'] ? 'selected' : '' ?>>
                        <?= $categoria['nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="preco" class="form-label">Preço *</label>
            <input type="text" id="preco" name="preco" class="form-control" required value="<?= number_format($produto['preco'], 2, ',', '.') ?>">
            <small class="form-text">Use vírgula como separador decimal (ex: 99,90)</small>
        </div>
        
        <div class="form-group">
            <label for="estoque" class="form-label">Estoque *</label>
            <input type="number" id="estoque" name="estoque" class="form-control" required value="<?= $produto['estoque'] ?>" min="0">
        </div>
        
        <div class="form-group">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea id="descricao" name="descricao" class="form-control" rows="5"><?= $produto['descricao'] ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="imagem" class="form-label">Imagem do Produto</label>
            <input type="file" id="imagem" name="imagem" class="form-control">
            <small class="form-text">Formatos aceitos: JPG, JPEG, PNG, GIF. Deixe em branco para manter a imagem atual.</small>
        </div>
        
        <div class="form-group">
            <?php if (!empty($produto['imagem'])): ?>
                <div class="current-image">
                    <p>Imagem atual:</p>
                    <img src="<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>" style="max-width: 200px; max-height: 200px;">
                </div>
            <?php endif; ?>
            <img id="image-preview" src="#" alt="Prévia da nova imagem" style="max-width: 200px; max-height: 200px; display: none; margin-top: 10px;">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Atualizar Produto</button>
        </div>
    </form>
</div>

<?php require_once '../../includes/admin-footer.php'; ?>
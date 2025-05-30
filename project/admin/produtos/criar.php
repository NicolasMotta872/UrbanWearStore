<?php
require_once '../../includes/admin-header.php';

// Buscar categorias
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
$categorias = $stmt->fetchAll();

// Verificar se existem categorias
if (count($categorias) === 0) {
    $_SESSION['flash_message'] = "Você precisa criar pelo menos uma categoria antes de adicionar produtos";
    $_SESSION['flash_type'] = "warning";
    header('Location: /project/admin/categorias/criar.php');
    exit;
}

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
    
    // Processar upload de imagem
    $imagem = '';
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
            } else {
                $erro = true;
                $mensagens[] = "Erro ao fazer upload da imagem";
            }
        }
    }
    
    // Se não há erros, inserir produto
    if (!$erro) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO produtos (nome, descricao, preco, imagem, estoque, categoria_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nome, $descricao, $preco, $imagem, $estoque, $categoria_id]);
            
            $_SESSION['flash_message'] = "Produto criado com sucesso!";
            $_SESSION['flash_type'] = "success";
            
            header("Location: /project/admin/produtos/index.php");
            exit;
        } catch (PDOException $e) {
            $erro = true;
            $mensagens[] = "Erro ao criar produto: " . $e->getMessage();
        }
    }
}
?>

<div class="admin-header">
    <h1 class="admin-title">Novo Produto</h1>
    <a href="http://localhost/project/admin/prddutos/index.php" class="btn btn-secondary">Voltar</a>
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
            <input type="text" id="nome" name="nome" class="form-control" required value="<?= isset($nome) ? $nome : '' ?>">
        </div>
        
        <div class="form-group">
            <label for="categoria_id" class="form-label">Categoria *</label>
            <select id="categoria_id" name="categoria_id" class="form-control" required>
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria['id'] ?>" <?= isset($categoria_id) && $categoria_id == $categoria['id'] ? 'selected' : '' ?>>
                        <?= $categoria['nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="preco" class="form-label">Preço *</label>
            <input type="text" id="preco" name="preco" class="form-control" required value="<?= isset($preco) ? $preco : '' ?>" placeholder="0,00">
            <small class="form-text">Use vírgula como separador decimal (ex: 99,90)</small>
        </div>
        
        <div class="form-group">
            <label for="estoque" class="form-label">Estoque *</label>
            <input type="number" id="estoque" name="estoque" class="form-control" required value="<?= isset($estoque) ? $estoque : '0' ?>" min="0">
        </div>
        
        <div class="form-group">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea id="descricao" name="descricao" class="form-control" rows="5"><?= isset($descricao) ? $descricao : '' ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="imagem" class="form-label">Imagem do Produto</label>
            <input type="file" id="imagem" name="imagem" class="form-control">
            <small class="form-text">Formatos aceitos: JPG, JPEG, PNG, GIF</small>
        </div>
        
        <div class="form-group">
            <img id="image-preview" src="#" alt="Prévia da imagem" style="max-width: 200px; max-height: 200px; display: none;">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Salvar Produto</button>
        </div>
    </form>
</div>

<?php require_once '../../includes/admin-footer.php'; ?>
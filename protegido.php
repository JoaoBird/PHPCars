<?php
session_start();
include_once 'dados.php';
include_once 'funcoes.php';

// Verificar se o usuário está logado
verificarLogin();

// Inicializar variáveis para edição
$modo = 'cadastro';
$carro_editando = null;
$id_editar = isset($_GET['editar']) ? intval($_GET['editar']) : 0;

// Carregar carro para edição se necessário
if ($id_editar > 0 && isset($_SESSION['carros_adicionados'])) {
    foreach ($_SESSION['carros_adicionados'] as $carro) {
        if ($carro['id'] == $id_editar) {
            $carro_editando = $carro;
            $modo = 'edicao';
            break;
        }
    }
}

// Processar exclusão
if (isset($_GET['excluir']) && !empty($_GET['excluir'])) {
    $id_excluir = intval($_GET['excluir']);
    
    if (excluirCarro($id_excluir)) {
        $mensagem = 'Carro excluído com sucesso!';
        // Redirecionar para evitar reexclusão ao atualizar a página
        header('Location: protegido.php?mensagem=' . urlencode($mensagem));
        exit;
    } else {
        $mensagem = 'Erro ao excluir carro.';
    }
}

// Processar mensagem de redirecionamento
if (isset($_GET['mensagem'])) {
    $mensagem = $_GET['mensagem'];
}

// Processar formulário de cadastro/edição
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar dados
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $marca = isset($_POST['marca']) ? trim($_POST['marca']) : '';
    $modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';
    $ano = isset($_POST['ano']) ? trim($_POST['ano']) : '';
    $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
    $preco = isset($_POST['preco']) ? floatval($_POST['preco']) : 0;
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    // Inicializar imagem com valor padrão ou existente
    $imagem = 'img/carros/default.jpg';
    if ($carro_editando && !empty($carro_editando['imagem'])) {
        $imagem = $carro_editando['imagem'];
    }
    
    // Processar upload de imagem
    if (isset($_FILES['imagem_upload']) && $_FILES['imagem_upload']['error'] == 0) {
        $upload_dir = 'img/carros/';
        $nome_arquivo = uniqid() . '_' . basename($_FILES['imagem_upload']['name']);
        $caminho_completo = $upload_dir . $nome_arquivo;
        
        // Verificar tipo de arquivo
        $permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (in_array($_FILES['imagem_upload']['type'], $permitidos)) {
            if (move_uploaded_file($_FILES['imagem_upload']['tmp_name'], $caminho_completo)) {
                $imagem = $caminho_completo;
            } else {
                $mensagem = 'Erro ao fazer upload da imagem.';
            }
        } else {
            $mensagem = 'Tipo de arquivo não permitido. Use apenas JPG, PNG ou WEBP.';
        }
    } else if (isset($_POST['imagem']) && !empty($_POST['imagem'])) {
        // Usar URL da imagem inserida manualmente se nenhum arquivo foi enviado
        $imagem = trim($_POST['imagem']);
    }
    
    // Validação simples
    if (empty($titulo) || empty($marca) || empty($modelo) || empty($ano) || 
        empty($categoria) || $preco <= 0) {
        $mensagem = 'Por favor, preencha todos os campos obrigatórios.';
    } else {
        // Criar array do carro
        $carro_dados = [
            'titulo' => $titulo,
            'marca' => $marca,
            'modelo' => $modelo,
            'ano' => $ano,
            'categoria' => $categoria,
            'preco' => $preco,
            'imagem' => $imagem
        ];
        
        if ($id > 0) {
            // Modo edição
            $carro_dados['id'] = $id;
            if (editarCarro($carro_dados)) {
                $mensagem = 'Carro atualizado com sucesso!';
                // Redirecionar para evitar reenvio do formulário
                header('Location: protegido.php?mensagem=' . urlencode($mensagem));
                exit;
            } else {
                $mensagem = 'Erro ao atualizar carro.';
            }
        } else {
            // Modo cadastro
            if (adicionarCarro($carro_dados)) {
                $mensagem = 'Carro cadastrado com sucesso!';
                // Redirecionar para evitar reenvio do formulário
                header('Location: protegido.php?mensagem=' . urlencode($mensagem));
                exit;
            } else {
                $mensagem = 'Erro ao cadastrar carro.';
            }
        }
    }
}

include_once 'header.php';
?>
<link rel="stylesheet" href="./css/Protegido.css">
<div class="container area-protegida">
    <h2 class="area-protegida-titulo">Área Protegida - Bem-vindo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    
    <div class="card">
        <h3><?php echo $modo === 'edicao' ? 'Editar Carro' : 'Cadastrar Novo Carro'; ?></h3>
        
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem"><?php echo $mensagem; ?></div>
        <?php endif; ?>
        
        <form method="post" action="protegido.php<?php echo $modo === 'edicao' ? '?editar=' . $id_editar : ''; ?>" enctype="multipart/form-data">
            <?php if ($modo === 'edicao'): ?>
                <input type="hidden" name="id" value="<?php echo $carro_editando['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required value="<?php echo $modo === 'edicao' ? htmlspecialchars($carro_editando['titulo']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="marca">Marca:</label>
                <input type="text" id="marca" name="marca" required value="<?php echo $modo === 'edicao' ? htmlspecialchars($carro_editando['marca']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="modelo">Modelo:</label>
                <input type="text" id="modelo" name="modelo" required value="<?php echo $modo === 'edicao' ? htmlspecialchars($carro_editando['modelo']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="ano">Ano:</label>
                <input type="text" id="ano" name="ano" required value="<?php echo $modo === 'edicao' ? htmlspecialchars($carro_editando['ano']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="categoria">Categoria:</label>
                <select id="categoria" name="categoria" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($modo === 'edicao' && $carro_editando['categoria'] === $cat) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="preco">Preço (R$):</label>
                <input type="number" id="preco" name="preco" min="1" step="0.01" required value="<?php echo $modo === 'edicao' ? $carro_editando['preco'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="imagem_upload">Imagem do Veículo:</label>
                <input type="file" id="imagem_upload" name="imagem_upload" accept="image/jpeg,image/jpg,image/png,image/webp" class="file-input">
                <div class="file-custom">Escolher arquivo</div>
                
                <div class="separator">ou</div>
                
                <label for="imagem">URL da Imagem:</label>
                <input type="text" id="imagem" name="imagem" placeholder="img/carros/default.jpg" value="<?php echo $modo === 'edicao' ? htmlspecialchars($carro_editando['imagem']) : ''; ?>">
                <small>Deixe em branco para usar imagem padrão ou faça upload acima</small>
                
                <?php if ($modo === 'edicao' && !empty($carro_editando['imagem'])): ?>
                <div class="image-preview">
                    <p>Imagem atual:</p>
                    <img src="<?php echo htmlspecialchars($carro_editando['imagem']); ?>" alt="Imagem atual">
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?php echo $modo === 'edicao' ? 'Atualizar' : 'Cadastrar'; ?> Carro</button>
                <?php if ($modo === 'edicao'): ?>
                    <a href="protegido.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Listar carros adicionados pelo usuário -->
    <div class="user-cars">
        <h3>Seus Carros Cadastrados</h3>
        
        <?php if (isset($_SESSION['carros_adicionados']) && !empty($_SESSION['carros_adicionados'])): ?>
            <div class="grid-container">
                <?php foreach ($_SESSION['carros_adicionados'] as $carro): ?>
                    <div class="grid-item">
                        <img src="<?php echo htmlspecialchars($carro['imagem']); ?>" alt="<?php echo htmlspecialchars($carro['titulo']); ?>">
                        <h4><?php echo htmlspecialchars($carro['titulo']); ?></h4>
                        <p>Ano: <?php echo htmlspecialchars($carro['ano']); ?></p>
                        <p>R$ <?php echo number_format($carro['preco'], 2, ',', '.'); ?></p>
                        <div class="car-actions">
                            <a href="detalhes.php?id=<?php echo $carro['id']; ?>" class="action-btn view-btn">Visualizar</a>
                            <a href="protegido.php?editar=<?php echo $carro['id']; ?>" class="action-btn edit-btn">Editar</a>
                            <a href="javascript:void(0)" onclick="confirmarExclusao(<?php echo $carro['id']; ?>, '<?php echo addslashes($carro['titulo']); ?>')" class="action-btn delete-btn">Excluir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-cars-message">Você ainda não cadastrou nenhum carro.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmarExclusao(id, titulo) {
    if (confirm('Tem certeza que deseja excluir o carro "' + titulo + '"?')) {
        window.location.href = 'protegido.php?excluir=' + id;
    }
}

// Garante que o clique no botão customizado abre o seletor de arquivos
document.querySelector('.file-custom').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('imagem_upload').click();
});

// Atualiza o texto do botão quando arquivo é selecionado
document.getElementById('imagem_upload').addEventListener('change', function() {
    if (this.files.length > 0) {
        document.querySelector('.file-custom').textContent = this.files[0].name;
    } else {
        document.querySelector('.file-custom').textContent = 'Escolher arquivo';
    }
});
</script>

<?php include_once 'footer.php'; ?>
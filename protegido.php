<?php
session_start();
include_once 'dados.php';
include_once 'funcoes.php';

// Verificar se o usuário está logado
verificarLogin();

// Processar cadastro de novo carro
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar dados
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $marca = isset($_POST['marca']) ? trim($_POST['marca']) : '';
    $modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';
    $ano = isset($_POST['ano']) ? trim($_POST['ano']) : '';
    $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
    $preco = isset($_POST['preco']) ? floatval($_POST['preco']) : 0;
    $imagem = isset($_POST['imagem']) && !empty($_POST['imagem']) ? 
              trim($_POST['imagem']) : 'img/carros/default.jpg';
    
    // Validação simples
    if (empty($titulo) || empty($marca) || empty($modelo) || empty($ano) || 
        empty($categoria) || $preco <= 0) {
        $mensagem = 'Por favor, preencha todos os campos obrigatórios.';
    } else {
        // Criar novo carro
        $novo_carro = [
            'titulo' => $titulo,
            'marca' => $marca,
            'modelo' => $modelo,
            'ano' => $ano,
            'categoria' => $categoria,
            'preco' => $preco,
            'imagem' => $imagem
        ];
        
        // Adicionar carro usando a função do arquivo funcoes.php
        if (adicionarCarro($novo_carro)) {
            $mensagem = 'Carro cadastrado com sucesso!';
        } else {
            $mensagem = 'Erro ao cadastrar carro.';
        }
    }
}

include_once 'header.php';
?>

<div class="container">
    <h2>Área Protegida - Bem-vindo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    
    <div class="card">
        <h3>Cadastrar Novo Carro</h3>
        
        <?php if ($mensagem): ?>
            <div class="mensagem"><?php echo $mensagem; ?></div>
        <?php endif; ?>
        
        <form method="post" action="protegido.php">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            
            <div class="form-group">
                <label for="marca">Marca:</label>
                <input type="text" id="marca" name="marca" required>
            </div>
            
            <div class="form-group">
                <label for="modelo">Modelo:</label>
                <input type="text" id="modelo" name="modelo" required>
            </div>
            
            <div class="form-group">
                <label for="ano">Ano:</label>
                <input type="text" id="ano" name="ano" required>
            </div>
            
            <div class="form-group">
                <label for="categoria">Categoria:</label>
                <select id="categoria" name="categoria" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="preco">Preço (R$):</label>
                <input type="number" id="preco" name="preco" min="1" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="imagem">URL da Imagem:</label>
                <input type="text" id="imagem" name="imagem" placeholder="img/carros/default.jpg">
                <small>Deixe em branco para usar imagem padrão</small>
            </div>
            
            <button type="submit" class="btn">Cadastrar Carro</button>
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
                        <h3><?php echo htmlspecialchars($carro['titulo']); ?></h3>
                        <p>Ano: <?php echo htmlspecialchars($carro['ano']); ?></p>
                        <p>R$ <?php echo number_format($carro['preco'], 2, ',', '.'); ?></p>
                        <a href="detalhes.php?id=<?php echo $carro['id']; ?>" class="enter-car-btn">Ver Detalhes</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Você ainda não cadastrou nenhum carro.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'footer.php'; ?>
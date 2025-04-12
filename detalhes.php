<?php
session_start();
include_once 'dados.php';
include_once 'funcoes.php';

// Definir funções necessárias caso não existam no arquivo funcoes.php
if (!function_exists('usuarioLogado')) {
    function usuarioLogado() {
        return isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true;
    }
}

if (!function_exists('podeDarLance')) {
    function podeDarLance($id) {
        // Implementar lógica para verificar se usuário pode dar lance
        // Por exemplo, verificar se o carro não pertence ao usuário logado
        return true; // Substitua pela sua lógica
    }
}

// Obter ID do carro
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Buscar carro pelo ID
$carro = buscarCarroPorId($carros, $id);

// Se não encontrar nos carros padrão, buscar nos adicionados
if (!$carro && isset($_SESSION['carros_adicionados']) && is_array($_SESSION['carros_adicionados'])) {
    $carro = buscarCarroPorId($_SESSION['carros_adicionados'], $id);
}

// Se carro não for encontrado, redirecionar para a página inicial
if (!$carro) {
    header('Location: index.php');
    exit;
}

// Incluir o header antes de qualquer saída HTML
include_once 'header.php';

// Verificar se o usuário pode dar lance - corrigido para usar $id em vez de $id_carro
$pode_dar_lance = podeDarLance($id);
?>

<div class="car-details">
    <h1><?php echo htmlspecialchars($carro['titulo']); ?></h1>
    
    <img class="car-image" src="<?php echo htmlspecialchars($carro['imagem']); ?>" 
         alt="<?php echo htmlspecialchars($carro['titulo']); ?>">
    
    <div class="car-info">
        <p><strong>Marca:</strong> <?php echo htmlspecialchars($carro['marca']); ?></p>
        <p><strong>Modelo:</strong> <?php echo htmlspecialchars($carro['modelo']); ?></p>
        <p><strong>Ano:</strong> <?php echo htmlspecialchars($carro['ano']); ?></p>
        <p><strong>Categoria:</strong> <?php echo htmlspecialchars($carro['categoria']); ?></p>
        <p><strong>Preço:</strong> R$ <?php echo number_format($carro['preco'], 2, ',', '.'); ?></p>
        
        <?php if (isset($carro['descricao'])): ?>
            <div class="car-description">
                <h3>Descrição</h3>
                <p><?php echo nl2br(htmlspecialchars($carro['descricao'])); ?></p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="specs-grid">
        <div class="spec-item">
            <h3>Motor</h3>
            <p><?php echo isset($carro['motor']) ? htmlspecialchars($carro['motor']) : '1.6'; ?></p>
        </div>
        <div class="spec-item">
            <h3>Quilometragem</h3>
            <p><?php echo isset($carro['km']) ? htmlspecialchars($carro['km']) : '85.000 km'; ?></p>
        </div>
        <div class="spec-item">
            <h3>Câmbio</h3>
            <p><?php echo isset($carro['cambio']) ? htmlspecialchars($carro['cambio']) : 'Manual'; ?></p>
        </div>
        <div class="spec-item">
            <h3>Combustível</h3>
            <p><?php echo isset($carro['combustivel']) ? htmlspecialchars($carro['combustivel']) : 'Gasolina'; ?></p>
        </div>
    </div>
    
    <?php if (usuarioLogado()): ?>
        <?php if ($pode_dar_lance): ?>
            <!-- Interface de lances (simulação) -->
            <div class="bid-section">
                <h3>Fazer Lance</h3>
                <form action="#" method="post">
                    <input type="hidden" name="carro_id" value="<?php echo $id; ?>">
                    <input type="number" name="valor" class="bid-input" 
                           placeholder="Digite seu lance (R$)" min="<?php echo $carro['preco']; ?>" step="100">
                    <button type="submit" class="bid-button">Fazer Lance</button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">Você não pode dar lance em seu próprio carro!</div>
        <?php endif; ?>
        
        <!-- Simulação de lances anteriores -->
        <div class="current-bids">
            <h3>Lances Atuais</h3>
            <div class="bid-item">
                <span class="bid-user">usuario123</span>
                <span class="bid-amount">R$ <?php echo number_format($carro['preco'] - 500, 2, ',', '.'); ?></span>
                <span class="bid-time">há 2 horas</span>
            </div>
            <div class="bid-item">
                <span class="bid-user">colecionador</span>
                <span class="bid-amount">R$ <?php echo number_format($carro['preco'] - 1000, 2, ',', '.'); ?></span>
                <span class="bid-time">há 4 horas</span>
            </div>
        </div>
    <?php else: ?>
        <div class="login-prompt">
            <p>Faça <a href="login.php">login</a> para dar lances neste veículo.</p>
        </div>
    <?php endif; ?>
    
    <div style="text-align: center; margin-top: 30px;">
        <a href="index.php" class="back-button">Voltar para Listagem</a>
    </div>
</div>

<?php include_once 'footer.php'; ?>
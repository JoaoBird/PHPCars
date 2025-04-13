<?php
session_start();
include_once 'dados.php';
include_once 'funcoes.php';


// Obter ID do carro
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Se não há ID, redirecionar
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Buscar carro diretamente usando a função de busca unificada
$carro = buscarCarro($id);

// Se carro não for encontrado, redirecionar para a página inicial
if (!$carro) {
    // Para debug - remova após testar
    echo "Carro não encontrado: ID = $id<br>";
    echo "Dados disponíveis: <pre>";
    print_r(carregarCarros());
    echo "</pre>";
    exit;
    
    // Descomente após testar
     header('Location: index.php');
     exit;
}

// Verificar se o usuário pode dar lance
$pode_dar_lance = podeDarLance($id);

// Incluir o header antes de qualquer saída HTML
include_once 'header.php';


// Preparar as imagens para exibição
$imagens = [];
if (isset($carro['imagens']) && is_array($carro['imagens'])) {
    // Se há um array de imagens, use-o
    $imagens = $carro['imagens'];
} else if (!empty($carro['imagem'])) {
    // Se há apenas uma imagem, coloque-a em um array
    $imagens[] = $carro['imagem'];
} else {
    // Se não há imagens, use a imagem padrão
    $imagens[] = 'img/carros/default.jpg';
}

// Obter a imagem principal (a primeira do array)
$imagem_principal = !empty($imagens) ? $imagens[0] : 'img/carros/default.jpg';

// Preparar todas as imagens para a galeria
$imagens_galeria = $imagens;
?>

<link rel="stylesheet" href="css/detalhes.css">

<div class="container">
    <div class="car-details-container">
        <!-- Título e Informações Principais -->
        <div class="car-header">
            <h1><?php echo htmlspecialchars($carro['titulo']); ?></h1>
            <div class="car-subtitle">
                <span class="car-year"><?php echo htmlspecialchars($carro['ano']); ?></span>
                <span class="car-make-model"><?php echo htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']); ?></span>
            </div>
        </div>

        <!-- Galeria de Imagens -->
        <div class="car-gallery">
            <!-- Imagem Principal -->
            <div class="main-image">
                <img src="<?php echo htmlspecialchars($imagem_principal); ?>" 
                     alt="<?php echo htmlspecialchars($carro['titulo']); ?>" 
                     id="main-car-image">
            </div>

            <!-- Miniaturas -->
            <div class="thumbnails">
                <?php foreach ($imagens_galeria as $index => $img): ?>
                <div class="thumbnail <?php echo ($index === 0) ? 'active' : ''; ?>" 
                     onclick="changeMainImage('<?php echo htmlspecialchars($img); ?>', this)">
                    <img src="<?php echo htmlspecialchars($img); ?>" 
                         alt="<?php echo htmlspecialchars($carro['titulo'] . ' - Imagem ' . ($index + 1)); ?>">
                </div>
                <?php endforeach; ?>
                
                <?php if (count($imagens_galeria) > 6): ?>
                <div class="all-photos-btn" onclick="showAllPhotos()">
                    Ver todas as fotos (<?php echo count($imagens_galeria); ?>)
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Informações do Carro - Layout Grid -->
        <div class="car-info-grid">
            <div class="info-column">
                <!-- Preço e Botões de Ação -->
                <div class="price-section">
                    <h2 class="car-price">R$ <?php echo number_format($carro['preco'], 2, ',', '.'); ?></h2>
                    
                    <?php if (usuarioLogado()): ?>
                        <?php if ($pode_dar_lance): ?>
                            <!-- Interface de lances -->
                            <div class="bid-section">
                                <form action="#" method="post" class="bid-form">
                                    <input type="hidden" name="carro_id" value="<?php echo $id; ?>">
                                    <input type="number" name="valor" class="bid-input" 
                                           placeholder="Digite seu lance (R$)" min="<?php echo $carro['preco']; ?>" step="100">
                                    <button type="submit" class="bid-button">Fazer Lance</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">Você não pode dar lance em seu próprio carro!</div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="login-prompt">
                            <a href="login.php" class="login-btn">Faça login para dar lances</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Detalhes Principais -->
                <div class="main-details">
                    <div class="detail-item">
                        <span class="detail-label">Marca</span>
                        <span class="detail-value"><?php echo htmlspecialchars($carro['marca']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Modelo</span>
                        <span class="detail-value"><?php echo htmlspecialchars($carro['modelo']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Ano</span>
                        <span class="detail-value"><?php echo htmlspecialchars($carro['ano']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Categoria</span>
                        <span class="detail-value"><?php echo htmlspecialchars($carro['categoria']); ?></span>
                    </div>
                </div>
                
                <!-- Especificações -->
                <div class="specs-grid">
                    <div class="spec-item">
                        <span class="spec-label">Motor</span>
                        <span class="detail-value"><?php echo htmlspecialchars($carro['motor']); ?></span>
                        </div>
                    <div class="spec-item">
                        <span class="spec-label">Quilometragem</span>
                        <span class="detail-value"><?php echo number_format($carro['quilometragem'], 0, ',', '.'); ?> km</span>
                        </div>
                    <div class="spec-item">
                        <span class="spec-label">Câmbio</span>
                        <span class="detail-value"><?php echo htmlspecialchars($carro['cambio']); ?></span>
                        </div>
                    <div class="spec-item">
                        <span class="spec-label">Combustível</span>
                        <span class="detail-value"><?php echo htmlspecialchars($carro['combustivel']); ?></span>
                        </div>
                </div>
  </div>
            
            <div class="info-column">
                <!-- Lances Atuais -->
                <?php if (usuarioLogado()): ?>
                <div class="current-bids">
                    <h3>Lances Atuais</h3>
                    <div class="bid-list">
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
                </div>
                <?php endif; ?>
                
                <!-- Descrição -->
                <?php if (isset($carro['descricao']) && !empty($carro['descricao'])): ?>
                <div class="car-description">
                    <h3>Descrição</h3>
                    <div class="description-content">
                        <?php echo nl2br(htmlspecialchars($carro['descricao'])); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Botão de Voltar -->
        <div class="back-button-container">
            <a href="index.php" class="back-button">Voltar para Listagem</a>
        </div>
    </div>
</div>

<!-- Modal para exibir todas as fotos -->
<div id="photosModal" class="photos-modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h2>Todas as Fotos</h2>
        <div class="modal-gallery">
            <?php foreach ($imagens_galeria as $index => $img): ?>
            <div class="modal-image">
                <img src="<?php echo htmlspecialchars($img); ?>" 
                     alt="<?php echo htmlspecialchars($carro['titulo'] . ' - Imagem ' . ($index + 1)); ?>">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
// Função para trocar a imagem principal
function changeMainImage(src, thumbnail) {
    document.getElementById('main-car-image').src = src;
    
    // Remover classe active de todas as miniaturas
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumb => {
        thumb.classList.remove('active');
    });
    
    // Adicionar classe active à miniatura clicada
    if (thumbnail) {
        thumbnail.classList.add('active');
    }
}

// Funções para o modal de fotos
function showAllPhotos() {
    document.getElementById('photosModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('photosModal').style.display = 'none';
}

// Fechar modal ao clicar fora dele
window.onclick = function(event) {
    const modal = document.getElementById('photosModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<?php include_once 'footer.php'; ?>
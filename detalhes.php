<?php
session_start();
include_once 'dados.php';
include_once 'funcoes.php';
include_once 'leiloes.php'; // Incluir o novo arquivo de leilões
include_once 'usuarios.php';
include_once 'login_fix.php';

// Obter ID do carro
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Se não há ID, redirecionar
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Buscar o carro pelo ID
$carro = buscarCarro($id);

// Se carro não for encontrado, redirecionar para a página inicial
if (!$carro || !is_array($carro)) {
    header('Location: index.php');
    exit;
}

// Agora que temos o carro, obter informações do leilão
global $leilaoManager;
$leilao = $leilaoManager->obterLeilao($id);

// Verificar se o usuário está logado
$logado = usuarioLogado();
$usuario_nome = $logado ? $_SESSION['username'] : "Não logado";

// Agora que temos o carro, verificar se o usuário pode dar lance
$pode_dar_lance = podeDarLance($id, $carro);
if ($logado) {
    $usuario_obj = buscarUsuario($_SESSION['username']);
    $saldo = $usuario_obj ? number_format($usuario_obj['saldo'], 2, ',', '.') : "N/A";
    
    // Garantir que usuario_id também esteja definido, se necessário
    if (!isset($_SESSION['usuario_id']) && isset($usuario_obj['id'])) {
        $_SESSION['usuario_id'] = $usuario_obj['id'];
    }
} else {
    $saldo = "N/A";
}

// Verificar se o usuário pode dar lance (usando a função do login_fix.php)
$pode_dar_lance = podeDarLance($id);

// Corrigir a verificação de se o usuário pode dar lance agora
$usuario_logado = usuarioLogado() ? $_SESSION['username'] : null;
$pode_dar_lance_agora = $usuario_logado && $leilaoManager->podeUsuarioDarLance($usuario_logado);

// Se carro não for encontrado, redirecionar para a página inicial
if (!$carro || !is_array($carro)) {
    header('Location: index.php');
    exit;
}

// Processar um lance se enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valor']) && $usuario_logado) {
    $valor_lance = floatval(str_replace(',', '.', $_POST['valor']));
    $resultado = $leilaoManager->registrarLance($id, $usuario_logado, $valor_lance);
    
    // Armazenar o resultado para exibir mensagem ao usuário
    $_SESSION['mensagem_lance'] = $resultado;
    
    // Redirecionar para evitar reenvio do formulário
    header('Location: detalhes.php?id=' . $id);
    exit;
}

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

if (!$leilao || !is_array($leilao)) {
    // Criar um leilão padrão ou redirecionar
    header('Location: index.php');
    exit;
}

$tempo_restante = $leilaoManager->formatarTempoRestante($leilao['data_fim']);
$leilao_finalizado = time() >= $leilao['data_fim'];
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
                
                <h2 class="car-price">R$ <?php echo number_format(isset($leilao['preco_atual']) ? $leilao['preco_atual'] : 0, 2, ',', '.'); ?></h2>                    
                    <?php if (isset($_SESSION['mensagem_lance'])): ?>
                        <div class="alert <?php echo $_SESSION['mensagem_lance']['status'] === 'sucesso' ? 'alert-success' : 'alert-warning'; ?>">
                            <?php echo $_SESSION['mensagem_lance']['mensagem']; ?>
                        </div>
                        <?php unset($_SESSION['mensagem_lance']); ?>
                    <?php endif; ?>
                    
                    <!-- Contador de tempo -->
                    <div class="time-remaining" style="margin-bottom: 15px;">
                        <span style="display: block; font-size: 14px; color: #a0aec0;">Tempo restante:</span>
                        <span style="font-weight: bold; font-size: 18px; <?php echo $leilao_finalizado ? 'color: #e74c3c;' : 'color: #2ecc71;'; ?>">
                            <?php echo $tempo_restante; ?>
                        </span>
                    </div>
                    
                    <?php if ($leilao_finalizado): ?>
                        <?php if ($leilao['vencedor']): ?>
                            <div class="alert alert-success" style="text-align: center; background-color: rgba(46, 204, 113, 0.2); border-left: 4px solid #2ecc71; color: #2ecc71;">
                                <strong>Leilão encerrado!</strong><br>
                                Vencedor: <strong><?php echo htmlspecialchars($leilao['vencedor']); ?></strong> com o lance de 
                                R$ <?php echo number_format(end($leilao['lances'])['valor'], 2, ',', '.'); ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning" style="text-align: center;">
                                <strong>Leilão encerrado sem lances!</strong>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (usuarioLogado()): ?>
                            <?php if ($pode_dar_lance): ?>
                                <!-- Interface de lances -->
                                <div class="bid-section">
                                    <form action="detalhes.php?id=<?php echo $id; ?>" method="post" class="bid-form">
                                        <input type="hidden" name="carro_id" value="<?php echo $id; ?>">
                                        <input type="number" name="valor" class="bid-input" 
                                               placeholder="Digite seu lance (R$)" 
                                               min="<?php echo $leilao['preco_atual'] + $leilao['incremento_minimo']; ?>" 
                                               step="100"
                                               <?php echo $pode_dar_lance_agora ? '' : 'disabled'; ?>>
                                        <button type="submit" class="bid-button" <?php echo $pode_dar_lance_agora ? '' : 'disabled'; ?>>
                                            Fazer Lance
                                        </button>
                                    </form>
                                    
                                    <?php if (!$pode_dar_lance_agora): ?>
                                        <div class="alert alert-warning" style="margin-top: 10px;">
                                            Você precisa esperar um pouco antes de dar outro lance
                                        </div>
                                    <?php else: ?>
                                        <div style="margin-top: 8px; font-size: 13px; color: #a0aec0;">
                                            * Lance mínimo: R$ <?php echo number_format($leilao['preco_atual'] + $leilao['incremento_minimo'], 2, ',', '.'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">Você não pode dar lance em seu próprio carro!</div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="login-prompt">
                                <a href="login.php" class="login-btn">Faça login para dar lances</a>
                            </div>
                        <?php endif; ?>
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
                <div class="current-bids">
                    <h3>Lances Atuais</h3>
                    <?php if (empty($leilao['lances'])): ?>
                        <div style="text-align: center; padding: 15px; color: #a0aec0;">
                            Nenhum lance registrado até o momento.
                        </div>
                    <?php else: ?>
                        <div class="bid-list">
                            <?php 
                            // Verificar se lances está definido e é um array
                            $lances = isset($leilao['lances']) && is_array($leilao['lances']) ? $leilao['lances'] : [];
                            
                            // Se tiver lances, ordená-los e exibi-los
                            if (!empty($lances)) {
                                // Ordenar lances do mais recente para o mais antigo
                                usort($lances, function($a, $b) {
                                    return $b['data'] - $a['data'];
                                });
                                
                                // Exibir os últimos 5 lances
                                $lances = array_slice($lances, 0, 5);
                                
                                // Exibir cada lance
                                foreach ($lances as $lance) {
                                    ?>
                                    <div class="bid-item <?php echo (isset($lance['usuario_real']) && $lance['usuario_real']) ? 'bid-real-user' : ''; ?>">
                                        <div>
                                            <span class="bid-user"><?php echo htmlspecialchars($lance['usuario']); ?></span>
                                            <span class="bid-time"><?php echo formatarTempoDecorrido($lance['data']); ?></span>
                                        </div>
                                        <span class="bid-amount">R$ <?php echo number_format($lance['valor'], 2, ',', '.'); ?></span>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Descrição -->
                <?php if (isset($carro['descricao']) && !empty($carro['descricao'])): ?>
                <div class="car-description">
                    <h3>Descrição</h3>
                    <div class="description-content">
                        <?php echo nl2br(htmlspecialchars($carro['descricao'])); ?>
                    </div>
                </div>
                <?php else: ?>
                    <div class="car-description">
                    <h3>Descrição</h3>
                    <div class="description-content">
                        <p>Este <?php echo htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']); ?> <?php echo htmlspecialchars($carro['ano']); ?> 
                        está em excelente estado de conservação, com documentação em dia e pronto para transferência.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
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
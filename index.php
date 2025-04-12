<?php 
// Carregar dados e funções 
include_once 'dados.php'; 
include_once 'funcoes.php';
include_once 'filtrar.php';

// Iniciar a sessão se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Processar filtros - com conversão de tipos
$filtro_texto = isset($_GET['busca_texto']) ? trim($_GET['busca_texto']) : '';
$filtro_categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$filtro_ano_min = isset($_GET['ano_min']) && is_numeric($_GET['ano_min']) ? (int)$_GET['ano_min'] : '';
$filtro_ano_max = isset($_GET['ano_max']) && is_numeric($_GET['ano_max']) ? (int)$_GET['ano_max'] : '';
$filtro_preco_min = isset($_GET['preco_min']) && is_numeric($_GET['preco_min']) ? (float)$_GET['preco_min'] : '';
$filtro_preco_max = isset($_GET['preco_max']) && is_numeric($_GET['preco_max']) ? (float)$_GET['preco_max'] : '';

// Para depuração - vamos mostrar quais filtros estão sendo aplicados
echo '<!-- Debug filtros: ';
echo 'Texto: ' . $filtro_texto . ', ';
echo 'Categoria: ' . $filtro_categoria . ', ';
echo 'Ano Min: ' . $filtro_ano_min . ', ';
echo 'Ano Max: ' . $filtro_ano_max . ', ';
echo 'Preço Min: ' . $filtro_preco_min . ', ';
echo 'Preço Max: ' . $filtro_preco_max;
echo ' -->';

// Limpar filtros se solicitado
if (isset($_GET['limpar_filtros'])) {
    header("Location: index.php");
    exit;
}

// Carregar todos os carros primeiro
$carros_persistentes = carregarCarros();

// Combinar com carros pré-definidos (se existirem)
$todos_carros = isset($carros) && is_array($carros) ? array_merge($carros, $carros_persistentes) : $carros_persistentes;

// Adicionar carros da sessão (se existirem)
if (isset($_SESSION['carros_adicionados']) && is_array($_SESSION['carros_adicionados'])) {
    foreach ($_SESSION['carros_adicionados'] as $carro) {
        $todos_carros[] = $carro;
    }
}

// Para depuração - vamos mostrar quantos carros temos antes dos filtros
echo '<!-- Total de carros antes dos filtros: ' . count($todos_carros) . ' -->';

// Aplicar os filtros em todos os carros
$todos_carros = aplicarFiltrosAvancados(
    $todos_carros,
    $filtro_texto,
    $filtro_categoria,
    $filtro_ano_min,
    $filtro_ano_max,
    $filtro_preco_min,
    $filtro_preco_max
);

// Para depuração - vamos mostrar quantos carros temos após os filtros
echo '<!-- Total de carros após os filtros: ' . count($todos_carros) . ' -->';

include_once 'header.php'; 

// Exibir mensagens de filtro (se houver)
echo exibirMensagensFiltro();
?>
<link rel="stylesheet" href="./css/styleindex.css">

<div class="layout-container">
    <!-- Coluna lateral de filtros usando a função do filtrar.php -->
    <?php 
    echo exibirFormularioFiltros(
        $categorias,
        $filtro_texto,
        $filtro_categoria,
        $filtro_ano_min,
        $filtro_ano_max,
        $filtro_preco_min,
        $filtro_preco_max
    ); 
    ?>
    
    <!-- Conteúdo principal - Grid de carros -->
    <div class="main-content">
        
        <!-- Barra de busca rápida para dispositivos móveis (opcional) -->
        <div class="mobile-search">
            <form action="" method="get" class="mobile-search-form">
                <input type="text" name="busca_texto" placeholder="Pesquisar carro..." 
                      value="<?php echo htmlspecialchars($filtro_texto); ?>" class="mobile-search-input">
                <button type="submit" class="mobile-search-button">Buscar</button>
            </form>
        </div>
        
        <?php
        // Exibir todos os carros
        if (!empty($todos_carros)): 
        ?>
<div class="grid-container">
    <?php foreach ($todos_carros as $carro): ?>
        <div class="grid-item">
            <img src="<?php echo htmlspecialchars($carro['imagem']); ?>" 
                 alt="<?php echo htmlspecialchars($carro['titulo']); ?>"
                 class="car-image">
            
            <div class="car-info">
                <div class="car-info-top">
                    <h3 class="car-title"><?php echo htmlspecialchars($carro['titulo']); ?></h3>
                    
                    <div class="car-details">
                        <!-- Exibir o ano do carro - para verificar o formato -->
                        <p class="car-year">Ano: <?php echo isset($carro['ano']) ? htmlspecialchars($carro['ano']) : 'N/D'; ?></p>
                    </div>
                    
                    <p class="car-description">
                        <?php echo htmlspecialchars($carro['descricao'] ?? 'Descrição padrão...'); ?>
                    </p>
                </div>
                
                <div class="car-bottom">
                    <div class="car-price">R$ <?php echo number_format($carro['preco'], 2, ',', '.'); ?></div>
                    <div class="car-actions">
                        <a href="detalhes.php?id=<?php echo $carro['id']; ?>" class="action-btn view-btn">Ver Detalhes</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
        <?php else: ?>
            <p class="no-cars-message">Nenhum carro encontrado com os filtros selecionados.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'footer.php'; ?>
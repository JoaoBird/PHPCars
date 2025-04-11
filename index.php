<?php 
// Carregar dados e funções 
include_once 'dados.php'; 
include_once 'funcoes.php';
include_once 'filtrar.php';

// Processar filtros
$filtro_ano_min = isset($_GET['ano_min']) ? $_GET['ano_min'] : '';
$filtro_ano_max = isset($_GET['ano_max']) ? $_GET['ano_max'] : '';
$filtro_preco_min = isset($_GET['preco_min']) ? $_GET['preco_min'] : '';
$filtro_preco_max = isset($_GET['preco_max']) ? $_GET['preco_max'] : '';
$filtro_texto = isset($_GET['busca_texto']) ? $_GET['busca_texto'] : '';
$filtro_categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Limpar filtros se solicitado
if (isset($_GET['limpar_filtros'])) {
    header("Location: index.php");
    exit;
}

// Aplicar filtros usando a função do filtrar.php
$carros_filtrados = aplicarFiltrosAvancados(
    $carros,
    $filtro_texto,
    $filtro_categoria,
    $filtro_ano_min,
    $filtro_ano_max,
    $filtro_preco_min,
    $filtro_preco_max
);

// Combinar carros padrão com os carros adicionados pelos usuários
$todos_carros = $carros_filtrados;

// Adicionar carros da sessão (se existirem)
if (isset($_SESSION['carros_adicionados']) && is_array($_SESSION['carros_adicionados'])) {
    foreach ($_SESSION['carros_adicionados'] as $carro) {
        $todos_carros[] = $carro;
    }
}

include_once 'header.php'; 

// Exibir mensagens de filtro (se houver)
echo exibirMensagensFiltro();
?>

<div class="layout-container">
    <!-- Coluna lateral de filtros usando a função do filtrar.php -->
    <?php echo exibirFormularioFiltros(
        $categorias,
        $filtro_texto,
        $filtro_categoria,
        $filtro_ano_min,
        $filtro_ano_max,
        $filtro_preco_min,
        $filtro_preco_max
    ); ?>
    
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
        
        <!-- Grid de carros -->
        <div class="grid-container">
            <?php if (empty($todos_carros)): ?>
                <div class="no-results">
                    <h3>Nenhum carro encontrado</h3>
                    <p>Tente outros filtros ou <a href="?limpar_filtros=1">limpar todos os filtros</a></p>
                </div>
            <?php else: ?>
                <?php foreach ($todos_carros as $carro): ?>
                    <div class="grid-item">
                        <img src="<?php echo htmlspecialchars($carro['imagem']); ?>" 
                             alt="<?php echo htmlspecialchars($carro['titulo']); ?>">
                        <h3><?php echo htmlspecialchars($carro['titulo']); ?></h3>
                        <p class="car-year">Ano: <?php echo htmlspecialchars($carro['ano']); ?></p>
                        <p class="car-price">R$ <?php echo number_format($carro['preco'], 2, ',', '.'); ?></p>
                        <a href="detalhes.php?id=<?php echo $carro['id']; ?>" class="enter-car-btn">Ver Detalhes</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Estilos para o layout de duas colunas */
.layout-container {
    display: flex;
    gap: 20px;
    padding: 20px;
    max-width: 1400px;
    margin: 0 200px 0 0px;
}

/* Estilo para a coluna lateral de filtros - movida mais à esquerda */
.sidebar-filters {
    width: 260px;
    flex-shrink: 0;
    background-color: #1a2135;
    border-radius: 10px;
    padding: 20px;
    height: fit-content;
    margin-left: 0px; /* Pequena margem à esquerda */
}

.sidebar-filters h2 {
    color: #fff;
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 20px;
}

.filter-group {
    margin-bottom: 20px;
}

.filter-group label {
    display: block;
    color: #fff;
    margin-bottom: 8px;
    font-weight: 500;
}

.filter-input, .filter-select {
    width: 100%;
    padding: 10px 15px;
    border-radius: 25px;
    border: 1px solid #384057;
    background-color: #2a324e;
    color: #fff;
    font-size: 14px;
}

.filter-range {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-input.small {
    width: calc(50% - 15px);
}

.range-separator {
    color: #fff;
    font-size: 14px;
}

.filter-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 30px;
}

.filter-btn {
    padding: 10px 15px;
    border-radius: 25px;
    text-align: center;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    border: none;
    font-size: 14px;
}

.filter-btn.apply {
    background-color: #32CD32;
    color: #fff;
}

.filter-btn.clear {
    background-color: transparent;
    border: 1px solid #7749F8;
    color: #7749F8;
}

/* Estilo para o conteúdo principal */
.main-content {
    flex-grow: 1;
}

.main-content h1 {
    color: #fff;
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 24px;
}

/* Esconder busca móvel em telas grandes */
.mobile-search {
    display: none;
    margin-bottom: 20px;
}

/* Grid de carros */
.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.grid-item {
    background-color: #1a2135;
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.grid-item:hover {
    transform: translateY(-5px);
}

.grid-item img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.grid-item h3 {
    padding: 10px 15px;
    margin: 0;
    color: #fff;
    font-size: 18px;
}

.car-year, .car-price {
    padding: 0 15px;
    margin: 5px 0;
    color: #b7c0d8;
}

.enter-car-btn {
    display: block;
    background-color: #7749F8;
    color: #fff;
    text-align: center;
    padding: 10px;
    margin: 15px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.enter-car-btn:hover {
    background-color: #6a3ce0;
}

.no-results {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px;
    background-color: #1a2135;
    border-radius: 10px;
}

.no-results h3 {
    color: #fff;
    margin-bottom: 10px;
}

.no-results a {
    color: #7749F8;
    text-decoration: none;
}

/* Mensagens de filtro */
.mensagens-filtro {
    margin-bottom: 20px;
    padding: 10px;
}

.mensagem-alerta {
    background-color: #2a324e;
    border-left: 4px solid #7749F8;
    padding: 10px 15px;
    margin-bottom: 10px;
    color: #fff;
    border-radius: 0 5px 5px 0;
}

/* Responsividade para dispositivos móveis */
@media (max-width: 768px) {
    .layout-container {
        flex-direction: column;
    }
    
    .sidebar-filters {
        width: 100%;
        order: 2;
        margin-left: 0;
    }
    
    .main-content {
        order: 1;
    }
    
    .mobile-search {
        display: block;
    }
    
    .mobile-search-form {
        display: flex;
        gap: 10px;
    }
    
    .mobile-search-input {
        flex-grow: 1;
        padding: 10px 15px;
        border-radius: 25px;
        border: 1px solid #384057;
        background-color: #2a324e;
        color: #fff;
    }
    
    .mobile-search-button {
        padding: 10px 15px;
        border-radius: 25px;
        background-color: #32CD32;
        color: #fff;
        border: none;
        cursor: pointer;
        font-weight: 600;
    }
}
</style>

<?php include_once 'footer.php'; ?>
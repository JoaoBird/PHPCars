<?php
// Carregar dados e funções
include_once 'dados.php';
include_once 'funcoes.php';

// Combinar carros padrão com os carros adicionados pelos usuários
$todos_carros = $carros_filtrados;

// Adicionar carros da sessão (se existirem)
if (isset($_SESSION['carros_adicionados']) && is_array($_SESSION['carros_adicionados'])) {
    foreach ($_SESSION['carros_adicionados'] as $carro) {
        $todos_carros[] = $carro;
    }
}

include_once 'header.php';
?>

<!-- Filtros rápidos -->
<div class="filter-section">
    <div class="dropdown">
        <a href="filtrar.php" class="filter-button">Filtros Avançados</a>
    </div>
    
    <div class="dropdown">
        <button class="filter-button" id="categoria-filter">Categoria</button>
        <div class="dropdown-content" id="categoria-dropdown">
            <?php foreach($categorias as $categoria): ?>
                <a href="?filtro_categoria=<?php echo urlencode($categoria); ?>"><?php echo htmlspecialchars($categoria); ?></a>
            <?php endforeach; ?>
            <a href="?limpar_filtros=1">Limpar Filtros</a>
        </div>
    </div>
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
                <img src="<?php echo htmlspecialchars($carro['imagem']); ?>" alt="<?php echo htmlspecialchars($carro['titulo']); ?>">
                <h3><?php echo htmlspecialchars($carro['titulo']); ?></h3>
                <p>Ano: <?php echo htmlspecialchars($carro['ano']); ?></p>
                <p>R$ <?php echo number_format($carro['preco'], 2, ',', '.'); ?></p>
                <a href="detalhes.php?id=<?php echo $carro['id']; ?>" class="enter-car-btn">Ver Detalhes</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>
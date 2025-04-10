<?php
// Iniciar a sessão se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se existem mensagens de filtro para exibir
if (isset($_SESSION['mensagens_filtro']) && !empty($_SESSION['mensagens_filtro'])) {
    echo '<div class="mensagens-filtro">';
    foreach ($_SESSION['mensagens_filtro'] as $mensagem) {
        echo '<div class="mensagem-alerta">' . htmlspecialchars($mensagem) . '</div>';
    }
    echo '</div>';
    
    // Limpar as mensagens após exibi-las
    unset($_SESSION['mensagens_filtro']);
}

include_once 'dados.php';
include_once 'funcoes.php';
include_once 'header.php';

// Já temos a lógica de filtragem no arquivo dados.php
// Aqui vamos apenas exibir o formulário de filtros avançados



// Extrair faixas de anos para o formulário
$anos = array_unique(array_column($carros, 'ano'));
sort($anos);
$ano_min = $anos[0];
$ano_max = $anos[count($anos) - 1];

// Extrair faixas de preços
$precos = array_column($carros, 'preco');
$preco_min = min($precos);
$preco_max = max($precos);
?>

<div class="container">
    <h2>Filtros Avançados</h2>
    
    <form action="index.php" method="get">
        <div class="form-group">
            <label for="filtro_texto">Busca por texto:</label>
            <input type="text" id="filtro_texto" name="filtro_texto" 
                   value="<?php echo isset($_GET['filtro_texto']) ? htmlspecialchars($_GET['filtro_texto']) : ''; ?>"
                   placeholder="Pesquisar marca, modelo...">
        </div>
        
        <div class="form-group">
            <label for="filtro_categoria">Categoria:</label>
            <select name="filtro_categoria" id="filtro_categoria">
                <option value="">Todas as categorias</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo htmlspecialchars($categoria); ?>" 
                    <?php echo (isset($_GET['filtro_categoria']) && $_GET['filtro_categoria'] === $categoria) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categoria); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Faixa de Ano:</label>
            <div class="range-inputs">
                <input type="number" name="filtro_ano_min" placeholder="Ano mínimo" 
                       min="<?php echo $ano_min; ?>" max="<?php echo $ano_max; ?>"
                       value="<?php echo isset($_GET['filtro_ano_min']) ? htmlspecialchars($_GET['filtro_ano_min']) : ''; ?>">
                <span>até</span>
                <input type="number" name="filtro_ano_max" placeholder="Ano máximo" 
                       min="<?php echo $ano_min; ?>" max="<?php echo $ano_max; ?>"
                       value="<?php echo isset($_GET['filtro_ano_max']) ? htmlspecialchars($_GET['filtro_ano_max']) : ''; ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Faixa de Preço:</label>
            <div class="range-inputs">
                <input type="number" name="filtro_preco_min" placeholder="Preço mínimo" 
                       min="0" step="100"
                       value="<?php echo isset($_GET['filtro_preco_min']) ? htmlspecialchars($_GET['filtro_preco_min']) : ''; ?>">
                <span>até</span>
                <input type="number" name="filtro_preco_max" placeholder="Preço máximo" 
                       min="0" step="100"
                       value="<?php echo isset($_GET['filtro_preco_max']) ? htmlspecialchars($_GET['filtro_preco_max']) : ''; ?>">
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Aplicar Filtros</button>
            <a href="index.php?limpar_filtros=1" class="btn btn-secondary">Limpar Filtros</a>
        </div>
    </form>
    
    <!-- Exibir carros filtrados (mesmo grid da página inicial) -->
    <h3>Carros Encontrados</h3>
    <div class="grid-container">
        <?php if (empty($carros_filtrados)): ?>
            <div class="no-results">
                <h3>Nenhum carro encontrado com os filtros selecionados</h3>
                <p>Tente outros filtros ou <a href="filtrar.php">limpar todos os filtros</a></p>
            </div>
        <?php else: ?>
            <?php foreach ($carros_filtrados as $carro): ?>
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
</div>

<?php include_once 'footer.php'; ?>
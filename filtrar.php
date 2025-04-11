<?php
// Iniciar a sessão se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função para filtrar os carros - renomeada para evitar conflito
function aplicarFiltrosAvancados($carros, $busca_texto = '', $categoria = '', $ano_min = '', $ano_max = '', $preco_min = '', $preco_max = '') {
    $carros_filtrados = $carros;
    
    // Filtrar por texto
    if (!empty($busca_texto)) {
        $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($busca_texto) {
            return (stripos($carro['titulo'], $busca_texto) !== false ||
                    stripos($carro['marca'], $busca_texto) !== false ||
                    stripos($carro['modelo'], $busca_texto) !== false);
        });
    }
    
    // Filtrar por categoria
    if (!empty($categoria)) {
        $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($categoria) {
            return $carro['categoria'] == $categoria;
        });
    }
    
    // Filtrar por ano
    if (!empty($ano_min)) {
        $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($ano_min) {
            return $carro['ano'] >= $ano_min;
        });
    }
    if (!empty($ano_max)) {
        $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($ano_max) {
            return $carro['ano'] <= $ano_max;
        });
    }
    
    // Filtrar por preço
    if (!empty($preco_min)) {
        $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($preco_min) {
            return $carro['preco'] >= $preco_min;
        });
    }
    if (!empty($preco_max)) {
        $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($preco_max) {
            return $carro['preco'] <= $preco_max;
        });
    }
    
    return $carros_filtrados;
}

// Verificar se existem mensagens de filtro para exibir
function exibirMensagensFiltro() {
    if (isset($_SESSION['mensagens_filtro']) && !empty($_SESSION['mensagens_filtro'])) {
        $html = '<div class="mensagens-filtro">';
        foreach ($_SESSION['mensagens_filtro'] as $mensagem) {
            $html .= '<div class="mensagem-alerta">' . htmlspecialchars($mensagem) . '</div>';
        }
        $html .= '</div>';
        
        // Limpar as mensagens após exibi-las
        unset($_SESSION['mensagens_filtro']);
        
        return $html;
    }
    return '';
}

// Função para gerar o HTML do formulário de filtros
function exibirFormularioFiltros($categorias, $filtro_texto = '', $filtro_categoria = '', $filtro_ano_min = '', $filtro_ano_max = '', $filtro_preco_min = '', $filtro_preco_max = '') {
    ob_start();
    ?>
    <div class="sidebar-filters">
        <h2>Filtros</h2>
        
            
        <form action="" method="get">
            <!-- Categoria -->
            <div class="filter-group">
                <label for="categoria">Categoria:</label>
                <select id="categoria" name="categoria" class="filter-select">
                    <option value="">Todas as categorias</option>
                    <?php foreach($categorias as $categoria): ?>
                        <option value="<?php echo htmlspecialchars($categoria); ?>" 
                                <?php echo ($filtro_categoria == $categoria) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Faixa de ano -->
            <div class="filter-group">
                <label>Faixa de Ano:</label>
                <div class="filter-range">
                    <input type="number" id="ano_min" name="ano_min" placeholder="Ano mín" 
                           value="<?php echo htmlspecialchars($filtro_ano_min); ?>" class="filter-input small">
                    <span class="range-separator">até</span>
                    <input type="number" id="ano_max" name="ano_max" placeholder="Ano máx" 
                           value="<?php echo htmlspecialchars($filtro_ano_max); ?>" class="filter-input small">
                </div>
            </div>
            
            <!-- Faixa de preço -->
            <div class="filter-group">
                <label>Faixa de Preço:</label>
                <div class="filter-range">
                    <input type="number" id="preco_min" name="preco_min" placeholder="R$ mín" 
                           value="<?php echo htmlspecialchars($filtro_preco_min); ?>" class="filter-input small">
                    <span class="range-separator">até</span>
                    <input type="number" id="preco_max" name="preco_max" placeholder="R$ máx" 
                           value="<?php echo htmlspecialchars($filtro_preco_max); ?>" class="filter-input small">
                </div>
            </div>
            
            <!-- Botões de filtro -->
            <div class="filter-buttons">
                <button type="submit" class="filter-btn apply">Aplicar Filtros</button>
                <a href="?limpar_filtros=1" class="filter-btn clear">Limpar Filtros</a>
            </div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
<?php
include_once 'carros_data.php';

// Verificar se há carros para exibir
if (empty($carros_filtrados)) {
    echo '<div class="no-results">';
    echo '<h3>Nenhum carro encontrado com os filtros selecionados</h3>';
    echo '<p>Tente outros filtros ou <a href="?limpar_filtros=1">limpar todos os filtros</a></p>';
    echo '</div>';
} else {
    // Exibir os carros filtrados
    foreach ($carros_filtrados as $carro) {
        $id = $carro['id'];
        $titulo = $carro['titulo'];
        $ano = $carro['ano'];
        $preco = $carro['preco'];
        $imagem = $carro['imagem'];
        
        echo "
        <div class=\"grid-item\">
          <img src=\"$imagem\" alt=\"$titulo\">
          <h3>$titulo</h3>
          <p>Ano: $ano</p>
          <p>R$ " . number_format($preco, 2, ',', '.') . "</p>
          <button class=\"enter-car-btn\" 
                 data-id=\"$id\" 
                 data-title=\"$titulo\" 
                 data-image=\"$imagem\">Ver Detalhes</button>
        </div>
        ";
    }
}
?>
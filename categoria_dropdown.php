<?php
include_once 'carros_data.php';

// Gerar o HTML para as categorias
foreach($categorias as $categoria) {
    echo "<a href=\"?filtro_categoria={$categoria}\">{$categoria}</a>";
}
echo "<a href=\"?limpar_filtros=1\">Limpar Filtros</a>";
?>
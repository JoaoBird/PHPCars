<?php
session_start();
include_once 'dados.php';
include_once 'funcoes.php';

// Pegar os parâmetros de filtro
$filtros = [];

if (!empty($_GET['marca'])) {
    $filtros['marca'] = $_GET['marca'];
}

if (!empty($_GET['categoria'])) {
    $filtros['categoria'] = $_GET['categoria'];
}

if (isset($_GET['ano_min']) && isset($_GET['ano_max'])) {
    $anoMin = $_GET['ano_min'];
    $anoMax = $_GET['ano_max'];
    
    // Filtrar por ano manualmente
    $carros = array_filter($carros, function($carro) use ($anoMin, $anoMax) {
        return isset($carro['ano']) && $carro['ano'] >= $anoMin && $carro['ano'] <= $anoMax;
    });
}

if (isset($_GET['preco_min']) && isset($_GET['preco_max'])) {
    $precoMin = floatval($_GET['preco_min']);
    $precoMax = floatval($_GET['preco_max']);
    
    // Filtrar por preço manualmente
    $carros = array_filter($carros, function($carro) use ($precoMin, $precoMax) {
        return isset($carro['preco']) && $carro['preco'] >= $precoMin && $carro['preco'] <= $precoMax;
    });
}

// Aplicar filtros
$carros_filtrados = filtrarCarros($carros, $filtros);

// Combinar com carros adicionados na sessão
if (isset($_SESSION['carros_adicionados']) && is_array($_SESSION['carros_adicionados'])) {
    $carros_adicionados_filtrados = filtrarCarros($_SESSION['carros_adicionados'], $filtros);
    $carros_filtrados = array_merge($carros_filtrados, $carros_adicionados_filtrados);
}

// Gerar HTML para os carros filtrados
foreach ($carros_filtrados as $carro) {
    $id = $carro['id'];
    $titulo = $carro['titulo'];
    $ano = $carro['ano'];
    $preco = isset($carro['preco']) ? $carro['preco'] : rand(5000, 50000);
    
    // Gerar imagem
    $imagem = isset($carro['imagem']) ? $carro['imagem'] : "img/carros/default.jpg";
    
    echo "
    <div class=\"grid-item\">
      <img src=\"$imagem\" alt=\"$titulo\">
      <h3>$titulo</h3>
      <p>Ano: $ano</p>
      <p>R$ " . number_format($preco, 2, ',', '.') . "</p>
      <button class=\"enter-car-btn\" data-id=\"$id\" data-title=\"$titulo\" data-image=\"$imagem\">Ver Detalhes</button>
    </div>
    ";
}
?>
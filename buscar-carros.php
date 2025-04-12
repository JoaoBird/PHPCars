<?php
session_start();
include_once 'carros_data.php';
include_once 'funcoes.php';

// Verificar se é uma busca por texto
if (isset($_GET['busca'])) {
    $busca = strtolower($_GET['busca']);
    
    // Filtrar carros pelo texto de busca
    $carros_filtrados = array_filter($carros, function($carro) use ($busca) {
        return strpos(strtolower($carro['titulo']), $busca) !== false || 
               strpos(strtolower($carro['marca']), $busca) !== false || 
               strpos(strtolower($carro['modelo']), $busca) !== false;
    });
    
    // Filtrar carros adicionados pela sessão
    $carros_adicionados_filtrados = [];
    if (isset($_SESSION['carros_adicionados']) && is_array($_SESSION['carros_adicionados'])) {
        $carros_adicionados_filtrados = array_filter($_SESSION['carros_adicionados'], function($carro) use ($busca) {
            return strpos(strtolower($carro['titulo']), $busca) !== false || 
                   strpos(strtolower($carro['marca']), $busca) !== false || 
                   strpos(strtolower($carro['modelo']), $busca) !== false;
        });
    }
    
    // Juntar os resultados
    $todos_carros_filtrados = array_merge($carros_filtrados, $carros_adicionados_filtrados);
    
    // Gerar HTML para os carros filtrados
    foreach ($todos_carros_filtrados as $carro) {
        $id = $carro['id'];
        $titulo = $carro['titulo'];
        $ano = $carro['ano'];
        $preco = isset($carro['preco']) ? $carro['preco'] : rand(5000, 50000);
        
        $imagem = isset($carro['imagem']) ? $carro['imagem'] : "https://via.placeholder.com/300x200?text={$carro['marca']}+{$carro['modelo']}";
        
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
    
    exit; // Termina o script aqui
}

// Se não for busca por texto, retorna todos os carros em formato JSON
$todos_carros = $carros;
if (isset($_SESSION['carros_adicionados']) && is_array($_SESSION['carros_adicionados'])) {
    $todos_carros = array_merge($todos_carros, $_SESSION['carros_adicionados']);
}

// Adicionar imagens e preços para demonstração
foreach ($todos_carros as &$carro) {
    if (!isset($carro['imagem'])) {
        $carro['imagem'] = "https://via.placeholder.com/300x200?text={$carro['marca']}+{$carro['modelo']}";
    }
    
    if (!isset($carro['preco'])) {
        $carro['preco'] = rand(5000, 50000);
    }
}

// Retornar como JSON
header('Content-Type: application/json');
echo json_encode($todos_carros);
?>
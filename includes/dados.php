<?php
// Definir as categorias
$categorias = [
    'Lata velha',
    'Tração traseira',
    'Clássico',
    'Popular',
    'Muscle car'
];

// Definir os carros
$carros = [
    [
        'id' => 1,
        'titulo' => "Volkswagen Fusca 1968",
        'ano' => "1968",
        'marca' => "Volkswagen",
        'modelo' => "Fusca",
        'categoria' => "Lata velha",
        'preco' => 15000.00,
        'imagem' => "img/carros/fusca.jpg"
    ],
    [
        'id' => 2,
        'titulo' => "Chevrolet Chevette 1991",
        'ano' => "1991",
        'marca' => "Chevrolet",
        'modelo' => "Chevette",
        'categoria' => "Tração traseira",
        'preco' => 8000.00,
        'imagem' => "img/carros/chevette.jpg"
    ],
    [
        'id' => 3,
        'titulo' => "Ford Mustang 1967",
        'ano' => "1967",
        'marca' => "Ford",
        'modelo' => "Mustang",
        'categoria' => "Clássico",
        'preco' => 120000.00,
        'imagem' => "img/carros/mustang.jpg"
    ],
    [
        'id' => 4,
        'titulo' => "Fiat Uno 1995",
        'ano' => "1995",
        'marca' => "Fiat",
        'modelo' => "Uno",
        'categoria' => "Popular",
        'preco' => 6500.00,
        'imagem' => "img/carros/uno.jpg"
    ],
    [
        'id' => 5,
        'titulo' => "Dodge Charger 1970",
        'ano' => "1970",
        'marca' => "Dodge",
        'modelo' => "Charger",
        'categoria' => "Muscle car",
        'preco' => 180000.00,
        'imagem' => "img/carros/charger.jpg"
    ]
];

// Aplicar filtros manualmente
$carros_filtrados = $carros;

// Processar filtro de texto (pesquisa)
if (isset($_GET['filtro_texto']) && !empty($_GET['filtro_texto'])) {
    $texto = strtolower($_GET['filtro_texto']);
    $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($texto) {
        return strpos(strtolower($carro['titulo']), $texto) !== false || 
               strpos(strtolower($carro['marca']), $texto) !== false || 
               strpos(strtolower($carro['modelo']), $texto) !== false;
    });
}

// Processar filtros da URL
if (isset($_GET['filtro_categoria']) && !empty($_GET['filtro_categoria'])) {
    $categoria_filtro = $_GET['filtro_categoria'];
    $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($categoria_filtro) {
        return $carro['categoria'] == $categoria_filtro;
    });
}

// Processar filtro de preço
if (isset($_GET['filtro_preco_min']) && isset($_GET['filtro_preco_max'])) {
    $precoMin = floatval($_GET['filtro_preco_min']);
    $precoMax = floatval($_GET['filtro_preco_max']);
    
    $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($precoMin, $precoMax) {
        return $carro['preco'] >= $precoMin && $carro['preco'] <= $precoMax;
    });
}

// Processar filtro de ano
if (isset($_GET['filtro_ano_min']) && isset($_GET['filtro_ano_max'])) {
    $anoMin = $_GET['filtro_ano_min'];
    $anoMax = $_GET['filtro_ano_max'];
    
    $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($anoMin, $anoMax) {
        return $carro['ano'] >= $anoMin && $carro['ano'] <= $anoMax;
    });
}

// Limpar filtros
if (isset($_GET['limpar_filtros'])) {
    // Redirecionar para a página sem parâmetros
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}
?>
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
        'imagem' => "img/carros/fusca.jpg",
        'quilometragem' => 85000,
        'motor' => "1.6",
        'cambio' => "Manual",
        'combustivel' => "Gasolina"
    ],
    [
        'id' => 2,
        'titulo' => "Chevrolet Chevette 1991",
        'ano' => "1991",
        'marca' => "Chevrolet",
        'modelo' => "Chevette",
        'categoria' => "Tração traseira",
        'preco' => 8000.00,
        'imagem' => "img/carros/chevette.jpg",
        'quilometragem' => 120000,
        'motor' => "1.4",
        'cambio' => "Manual",
        'combustivel' => "Álcool"
    ],
    [
        'id' => 3,
        'titulo' => "Ford Mustang 1967",
        'ano' => "1967",
        'marca' => "Ford",
        'modelo' => "Mustang",
        'categoria' => "Clássico",
        'preco' => 120000.00,
        'imagem' => "img/carros/mustang.jpg",
        'quilometragem' => 65000,
        'motor' => "5.0",
        'cambio' => "Manual",
        'combustivel' => "Gasolina"
    ],
    [
        'id' => 4,
        'titulo' => "Fiat Uno 1995",
        'ano' => "1995",
        'marca' => "Fiat",
        'modelo' => "Uno",
        'categoria' => "Popular",
        'preco' => 6500.00,
        'imagem' => "img/carros/uno.jpg",
        'quilometragem' => 180000,
        'motor' => "1.0",
        'cambio' => "Manual",
        'combustivel' => "Gasolina"
    ],
    [
        'id' => 5,
        'titulo' => "Dodge Charger 1970",
        'ano' => "1970",
        'marca' => "Dodge",
        'modelo' => "Charger",
        'categoria' => "Muscle car",
        'preco' => 180000.00,
        'imagem' => "img/carros/charger.jpg",
        'quilometragem' => 75000,
        'motor' => "7.2",
        'cambio' => "Manual",
        'combustivel' => "Gasolina"
    ]
];

// Aplicar filtros manualmente
$carros_filtrados = $carros;


// Variável para armazenar mensagens de erro ou aviso
$mensagens = [];

// Aplicar filtros manualmente
$carros_filtrados = $carros;

// Processar filtro de texto (pesquisa)
if (isset($_GET['filtro_texto']) && trim($_GET['filtro_texto']) !== '') {
    $texto = strtolower(trim($_GET['filtro_texto']));
    $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($texto) {
        return strpos(strtolower($carro['titulo']), $texto) !== false ||
               strpos(strtolower($carro['marca']), $texto) !== false ||
               strpos(strtolower($carro['modelo']), $texto) !== false;
    });
}

// Processar filtros da URL - Categoria
if (isset($_GET['filtro_categoria']) && trim($_GET['filtro_categoria']) !== '') {
    $categoria_filtro = trim($_GET['filtro_categoria']);
    $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($categoria_filtro) {
        return $carro['categoria'] == $categoria_filtro;
    });
}

// Processar filtro de preço - Versão melhorada
$aplicarFiltroPrecoMin = isset($_GET['filtro_preco_min']) && trim($_GET['filtro_preco_min']) !== '';
$aplicarFiltroPrecoMax = isset($_GET['filtro_preco_max']) && trim($_GET['filtro_preco_max']) !== '';

if ($aplicarFiltroPrecoMin || $aplicarFiltroPrecoMax) {
    // Valores padrão
    $precoMin = 0;
    $precoMax = PHP_FLOAT_MAX; // Valor máximo possível para float
    
    // Processar valor mínimo se fornecido
    if ($aplicarFiltroPrecoMin) {
        $precoMinStr = str_replace(',', '.', trim($_GET['filtro_preco_min']));
        if (is_numeric($precoMinStr)) {
            $precoMin = floatval($precoMinStr);
            if ($precoMin < 0) {
                $precoMin = 0;
                $mensagens[] = "O preço mínimo não pode ser negativo. Usando 0 como valor mínimo.";
            }
        } else {
            $mensagens[] = "O valor mínimo de preço não é um número válido. Usando 0 como valor mínimo.";
        }
    }
    
    // Processar valor máximo se fornecido
    if ($aplicarFiltroPrecoMax) {
        $precoMaxStr = str_replace(',', '.', trim($_GET['filtro_preco_max']));
        if (is_numeric($precoMaxStr)) {
            $precoMax = floatval($precoMaxStr);
            if ($precoMax < 0) {
                $precoMax = PHP_FLOAT_MAX;
                $mensagens[] = "O preço máximo não pode ser negativo. Usando valor máximo possível.";
            }
        } else {
            $mensagens[] = "O valor máximo de preço não é um número válido. Usando valor máximo possível.";
        }
    }
    
    // Garantir que min <= max
    if ($precoMin > $precoMax) {
        $temp = $precoMin;
        $precoMin = $precoMax;
        $precoMax = $temp;
        $mensagens[] = "Os valores de preço mínimo e máximo foram invertidos para garantir um intervalo válido.";
    }
    
    // Aplicar o filtro
    $carros_filtrados = array_filter($carros_filtrados, function($carro) use ($precoMin, $precoMax) {
        return $carro['preco'] >= $precoMin && $carro['preco'] <= $precoMax;
    });
}

// Processar filtro de ano - Versão melhorada
$aplicarFiltroAnoMin = isset($_GET['filtro_ano_min']) && trim($_GET['filtro_ano_min']) !== '';
$aplicarFiltroAnoMax = isset($_GET['filtro_ano_max']) && trim($_GET['filtro_ano_max']) !== '';

if ($aplicarFiltroAnoMin || $aplicarFiltroAnoMax) {
    // Valores padrão
    $anoMin = 1900; // Um ano razoavelmente antigo como padrão
    $anoMax = date('Y') + 1; // Ano atual + 1 como máximo padrão
    
    // Processar ano mínimo se fornecido
    if ($aplicarFiltroAnoMin) {
        $anoMinStr = trim($_GET['filtro_ano_min']);
        if (is_numeric($anoMinStr) && intval($anoMinStr) == $anoMinStr) { // Verifica se é um número inteiro
            $anoMin = intval($anoMinStr);
        } else {
            $mensagens[] = "O ano mínimo não é um número inteiro válido. Usando 1900 como ano mínimo.";
        }
    }
    
    // Processar ano máximo se fornecido
    if ($aplicarFiltroAnoMax) {
        $anoMaxStr = trim($_GET['filtro_ano_max']);
        if (is_numeric($anoMaxStr) && intval($anoMaxStr) == $anoMaxStr) { // Verifica se é um número inteiro
            $anoMax = intval($anoMaxStr);
        } else {
            $mensagens[] = "O ano máximo não é um número inteiro válido. Usando " . (date('Y') + 1) . " como ano máximo.";
        }
    }
    
    // Garantir que min <= max
    if ($anoMin > $anoMax) {
        $temp = $anoMin;
        $anoMin = $anoMax;
        $anoMax = $temp;
        $mensagens[] = "Os valores de ano mínimo e máximo foram invertidos para garantir um intervalo válido.";
    }
    
    // Aplicar o filtro
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

// Armazenar mensagens na sessão para exibir na próxima página
if (!empty($mensagens)) {
    session_start();
    $_SESSION['mensagens_filtro'] = $mensagens;
}
?>
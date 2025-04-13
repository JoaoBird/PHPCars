<?php
// Caminho para o arquivo JSON que armazenará os carros
define('ARQUIVO_CARROS', 'dados/carros.json');

//Verifica se o usuário está logado (necessita do session_start())
function usuarioLogado(){
    return isset($_SESSION['username']) && !empty($_SESSION['username']);
}

//Verifica o login e joga o usuário para uma página específica
function verificarLogin(){
    if(!usuarioLogado()){
        header('Location: login.php');
        exit;
    }
}

// Função para carregar carros do arquivo
function carregarCarros() {
    if (!file_exists(ARQUIVO_CARROS)) {
        // Garantir que o diretório existe
        if (!file_exists('dados')) {
            if (!mkdir('dados', 0755, true)) {
                // Se falhar em criar o diretório, registre um erro
                error_log('Falha ao criar diretório de dados');
            }
        }
        file_put_contents(ARQUIVO_CARROS, json_encode([]));
        return [];
    }
    
    $conteudo = file_get_contents(ARQUIVO_CARROS);
    if (empty($conteudo)) {
        return [];
    }
    
    return json_decode($conteudo, true);
}


function formatarTempoDecorrido($timestamp) {
    $agora = time();
    $diferenca = $agora - $timestamp;
    
    if ($diferenca < 60) {
        return "agora mesmo";
    } elseif ($diferenca < 3600) {
        $minutos = floor($diferenca / 60);
        return "há " . $minutos . ($minutos == 1 ? " minuto" : " minutos");
    } elseif ($diferenca < 86400) {
        $horas = floor($diferenca / 3600);
        return "há " . $horas . ($horas == 1 ? " hora" : " horas");
    } else {
        $dias = floor($diferenca / 86400);
        return "há " . $dias . ($dias == 1 ? " dia" : " dias");
    }
}

// Função para salvar carros no arquivo
function salvarCarros($carros) {
    // Garantir que o diretório existe
    if (!file_exists('dados')) {
        mkdir('dados', 0755, true);
    }
    return file_put_contents(ARQUIVO_CARROS, json_encode($carros));
}

//Função para filtrar carros
function filtrarCarros($carros, $filtrar = []){   
    if(empty($filtrar)){
        return $carros;
    }

    $resultado = [];

    foreach ($carros as $carro) {
        $incluir = true;

        foreach ($filtrar as $chave => $valor){
            if(!empty($valor) && isset($carro[$chave]) && $carro[$chave] != $valor){
                $incluir = false;
                break;
            }
        }

        if($incluir){
            $resultado[] = $carro;
        }
    }
    
    return $resultado;
}

//Função para busca direta por ID
function buscarCarroPorId($carros, $id) {
    foreach ($carros as $carro) {
        if (isset($carro['id']) && $carro['id'] == $id) {
            return $carro;
        }
    }
    return null;
}

// Verificar se o usuário logado é proprietário do carro
function usuarioEProprietario($id_carro) {
    if (!usuarioLogado() || !isset($_SESSION['user_id'])) {
        return false;
    }
    
    $carro = buscarCarro($id_carro);
    if ($carro && isset($carro['usuario_id']) && $carro['usuario_id'] == $_SESSION['user_id']) {
        return true;
    }
    
    return false;
}

// Verifica se o usuário pode dar um lance no carro
function podeDarLance($id_carro) {
    // Usuário não pode dar lance se não estiver logado
    if (!usuarioLogado()) {
        return false;
    }
    
    // Usuário não pode dar lance no próprio carro
    return !usuarioEProprietario($id_carro);
}

//Adiciona um novo carro
function adicionarCarro($carro){
    // Define o ID do usuário proprietário
    if (isset($_SESSION['user_id'])) {
        $carro['usuario_id'] = $_SESSION['user_id'];
    } else {
        // Identificação temporária para compatibilidade
        $carro['usuario_id'] = session_id();
    }
    
    // Carrega carros do arquivo
    $carros_arquivo = carregarCarros();
    
    // Encontrar o maior ID entre todos os carros
    // Começamos com 1000 para evitar conflitos com carros predefinidos
    $maior_id = 1000;
    
    // No arquivo
    foreach ($carros_arquivo as $c){
        if(isset($c['id']) && $c['id'] > $maior_id){
            $maior_id = $c['id'];
        }
    }
    
    // Adiciona o id ao carro
    $carro['id'] = $maior_id + 1;
    
    // Adiciona ao arquivo
    $carros_arquivo[] = $carro;
    
    // Salvar no arquivo
    $salvou = salvarCarros($carros_arquivo);
    
    // Retorna se salvou com sucesso
    return $salvou !== false;
}

// Função para editar um carro existente
function editarCarro($carro) {
    // Carrega carros do arquivo
    $carros = carregarCarros();
    $editado = false;
    
    // Editar no arquivo
    foreach ($carros as $key => $car) {
        if ($car['id'] == $carro['id']) {
            // Preservar o usuário_id original
            if (isset($car['usuario_id'])) {
                $carro['usuario_id'] = $car['usuario_id'];
            }
            $carros[$key] = $carro;
            $editado = true;
            break;
        }
    }
    
    if ($editado) {
        return salvarCarros($carros);
    }
    
    return false;
}

// Função para excluir um carro
function excluirCarro($id) {
    // Carrega carros do arquivo
    $carros = carregarCarros();
    $excluido = false;
    
    // Excluir do arquivo
    foreach ($carros as $key => $carro) {
        if ($carro['id'] == $id) {
            // Excluir arquivo de imagem se não for a imagem padrão
            if (!empty($carro['imagem']) && 
                $carro['imagem'] !== 'img/carros/default.jpg' && 
                strpos($carro['imagem'], 'img/carros/') === 0 && 
                file_exists($carro['imagem'])) {
                @unlink($carro['imagem']);
            }
            
            // Remover o carro do array
            unset($carros[$key]);
            $carros = array_values($carros);
            $excluido = true;
            break;
        }
    }
    
    if ($excluido) {
        return salvarCarros($carros);
    }
    
    return false;
}

// Função para buscar carro específico (útil para detalhes)
function buscarCarro($id) {
    if (empty($id) || $id <= 0) {
        return null;
    }
    
    // Verificar nos carros do arquivo
    $carros = carregarCarros();
    foreach ($carros as $carro) {
        if (isset($carro['id']) && (int)$carro['id'] === (int)$id) {
            return $carro;
        }
    }
    
    // Verificar nos carros pré-definidos globais
    global $carros;
    if (isset($carros) && is_array($carros)) {
        foreach ($carros as $carro) {
            if (isset($carro['id']) && (int)$carro['id'] === (int)$id) {
                return $carro;
            }
        }
    }
    
    return null;
}
// Verificar pastas de upload
function verificarPastasUpload() {
    $upload_dir = 'img/carros/';
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
}

// Verificar pastas ao carregar este arquivo
verificarPastasUpload();
?>
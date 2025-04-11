<!-- Arquivo com as funções do sistema -->
<?php 

//Função para filtrar carros
function filtrarCarros($carros, $filtrar = []){   //Entre as chaves você coloca a TAG de filtro
    
    //Se não tiver nenhum opção de filtro, retorna o array de carros
    if(empty($filtrar)){
        return $carros;
    }

    //Array com o resultado da filtragem
    $resultado = [];

    //Inclui os carros no array no array de resultado
    foreach ($carros as $carro) {
        $incluir = true;

        foreach ($filtrar as $chave => $valor){
            //Se o filtro tiver o valor, mas o carro não for o certo, não adiciona
            if(!empty($valor) && isset($carro[$chave]) && $carro[$chave] != $valor){
                $incluir = false;
                break;
            }
        }

        //Adiciona o carro atual ao array de resultado
        if($incluir){
            $resultado[] = $carro;
        }

    }
    
    return $resultado;
};

//Função para busca direta por ID
function buscarCarroPorId($carros, $id){
    foreach ($carros as $carro) {
        if($carro['id'] == $id){
            return $carro;
        }
    }

    return null;

};

//Verifica se o usuário está logado (necessita do session_start())
function usuarioLogado(){
    return isset($_SESSION['usuario_logado']) && ($_SESSION['usuario_logado'] === true);
}

//Verifica o login e joga o usuário para uma página específica
function verificarLogin(){
    if(!usuarioLogado()){
        header('Location: login.php');
        exit;
    }
}

//Adiciona um novo carro
function adicionarCarro($carro){
    //Verifica se já existe um array de carros na sessão
    if(!isset($_SESSION['carros_adicionados']) || !is_array($_SESSION['carros_adicionados'])){
        //Se não for um array, cria um vazio
        $_SESSION['carros_adicionados'] = [];
    }

    //Chama o array de carros
    global $carros;
    //Var para o id
    $maior_id = 0;

    //Encontrando o maior id no array de carros
    foreach ($carros as $c){
        if(isset($c['id']) && $c['id'] > $maior_id){
            $maior_id = $c['id'];
        };
    }

    //Checa o maior id da lista de carros adicionados à sessão, previamente.
    foreach ($_SESSION['carros_adicionados'] as $c){
        if(isset($c['id']) && $c['id'] > $maior_id){
            $maior_id = $c['id'];
        }
    }

    //Adiciona o id ao carro
    $carro['id'] = $maior_id + 1;

    //Adiciona o carro, com o id correto ao array da sessão
    $_SESSION['carros_adicionados'][] = $carro;

    //Confirmação que o carro foi adicionado
    return true;

}


// Função para editar um carro existente
function editarCarro($carro) {
    if (!isset($_SESSION['carros_adicionados']) || empty($_SESSION['carros_adicionados'])) {
        return false;
    }
    
    $editado = false;
    foreach ($_SESSION['carros_adicionados'] as $key => $car) {
        if ($car['id'] == $carro['id']) {
            $_SESSION['carros_adicionados'][$key] = $carro;
            $editado = true;
            break;
        }
    }
    
    return $editado;
}

// Função para excluir um carro
function excluirCarro($id) {
    if (!isset($_SESSION['carros_adicionados']) || empty($_SESSION['carros_adicionados'])) {
        return false;
    }
    
    $excluido = false;
    foreach ($_SESSION['carros_adicionados'] as $key => $carro) {
        if ($carro['id'] == $id) {
            // Excluir arquivo de imagem se não for a imagem padrão e o arquivo existir
            if (!empty($carro['imagem']) && 
                $carro['imagem'] !== 'img/carros/default.jpg' && 
                strpos($carro['imagem'], 'img/carros/') === 0 && 
                file_exists($carro['imagem'])) {
                @unlink($carro['imagem']);
            }
            
            // Remover o carro do array
            unset($_SESSION['carros_adicionados'][$key]);
            // Reorganizar os índices
            $_SESSION['carros_adicionados'] = array_values($_SESSION['carros_adicionados']);
            $excluido = true;
            break;
        }
    }
    
    return $excluido;
}

// Função para buscar um carro específico (útil para detalhes)
function buscarCarro($id) {
    // Verificar se o ID está na sessão
    if (isset($_SESSION['carros_adicionados']) && !empty($_SESSION['carros_adicionados'])) {
        foreach ($_SESSION['carros_adicionados'] as $carro) {
            if ($carro['id'] == $id) {
                return $carro;
            }
        }
    }
    
    // Verificar nos carros pré-definidos
    global $carros;
    foreach ($carros as $carro) {
        if ($carro['id'] == $id) {
            return $carro;
        }
    }
    
    return null;
}

// Função para criar pastas de upload se não existirem
function verificarPastasUpload() {
    $upload_dir = 'img/carros/';
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
}

// Verificar pastas de upload ao carregar este arquivo
verificarPastasUpload();

?>
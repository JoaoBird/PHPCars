<?php

// Garantir que a sessão esteja iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função unificada para verificar login - verificar se já existe antes de declarar
if (!function_exists('usuarioLogado')) {
    function usuarioLogado() {
        return isset($_SESSION['username']) && !empty($_SESSION['username']);
    }
}

// Função para buscar usuário de forma consistente
if (!function_exists('buscarUsuario')) {
    function buscarUsuario($identificador) {
        $usuarios = carregarUsuarios();
        
        if (!is_array($usuarios)) {
            return null;
        }
        
        foreach ($usuarios as $usuario) {
            if (
                // Verificar todas as possíveis chaves de identificação
                (isset($usuario['id']) && $usuario['id'] == $identificador) || 
                (isset($usuario['usuario_id']) && $usuario['usuario_id'] == $identificador) ||
                (isset($usuario['username']) && $usuario['username'] == $identificador) ||
                (isset($usuario['usuario']) && $usuario['usuario'] == $identificador)
            ) {
                return $usuario;
            }
        }
        
        return null;
    }
}

// Função para verificar se pode dar lance
if (!function_exists('podeDarLance')) {
    function podeDarLance($carro_id, $carro_atual = null) {
        // Se não estiver logado, não pode dar lance
        if (!usuarioLogado()) {
            return false;
        }
        
        // Usar o carro fornecido ou buscar um novo
        $carro = $carro_atual;
        if (!$carro) {
            $carro = buscarCarro($carro_id);
            if (!$carro) {
                return false; // Se não encontrar o carro, não pode dar lance
            }
        }
        
        if (isset($carro['usuario_id']) && isset($_SESSION['usuario_id']) && 
            $carro['usuario_id'] == $_SESSION['usuario_id']) {
            return false; // Proprietário não pode dar lance em seu próprio carro
        }
        
        // Buscar informações do leilão (se disponível)
        global $leilaoManager;
        if (isset($leilaoManager)) {
            $leilao = $leilaoManager->obterLeilao($carro_id);
            
            // Verificar se o leilão já terminou
            if ($leilao && time() >= $leilao['data_fim']) {
                return false;
            }
        }
        
        // Por padrão, permitir lance
        return true;
    }
}

// Normalizar as variáveis de sessão
if (isset($_SESSION['username']) && !isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = $_SESSION['username'];
}

if (isset($_SESSION['usuario']) && !isset($_SESSION['username'])) {
    $_SESSION['username'] = $_SESSION['usuario'];
}

// Garantir que temos um ID de usuário se estiver logado
if (usuarioLogado() && !isset($_SESSION['usuario_id'])) {
    $usuario = buscarUsuario($_SESSION['username']);
    if ($usuario && isset($usuario['id'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
    }
}


?>
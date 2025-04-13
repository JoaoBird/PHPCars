<?php
// Caminho para o arquivo de usuários
define('ARQUIVO_USUARIOS', 'dados/usuarios.json');

// Função para inicializar os usuários padrão caso o arquivo não exista
function inicializarUsuarios() {
    $usuarios_padrao = [
        [
            'username' => 'admin',
            // Hash fixo para 'admin123' - gerado uma única vez com password_hash
            'password' => '$2y$10$r69Q.qAH.UU4yLnJTSZ4COLyrAmu.ay7rDKKasVgmmBQtomoIlraW',
            'saldo' => 0.00,
            'id' => 1
        ],
        [
            'username' => 'user',
            // Hash fixo para 'user123' - gerado uma única vez com password_hash
            'password' => '$2y$10$ZGpiA/cmP.u4zE3mhJ.H2.nKBdyym2xYXpcLr.WshhCDb8vCvq0rq',
            'saldo' => 0.00,
            'id' => 2
        ]
    ];
    
    // Salvar os usuários padrão no arquivo
    if (!file_exists('dados')) {
        mkdir('dados', 0755, true);
    }
    
    file_put_contents(ARQUIVO_USUARIOS, json_encode($usuarios_padrao));
    
    return $usuarios_padrao;
}

// Função para carregar usuários
function carregarUsuarios() {
    if (!file_exists(ARQUIVO_USUARIOS)) {
        return inicializarUsuarios();
    }
    
    $conteudo = file_get_contents(ARQUIVO_USUARIOS);
    if (empty($conteudo)) {
        return inicializarUsuarios();
    }
    
    return json_decode($conteudo, true);
}

// Função para salvar usuários
function salvarUsuarios($usuarios) {
    if (!file_exists('dados')) {
        mkdir('dados', 0755, true);
    }
    
    return file_put_contents(ARQUIVO_USUARIOS, json_encode($usuarios));
}

// Função para buscar usuário pelo nome
function buscarUsuario($identificador) {
    $usuarios = carregarUsuarios();
    
    foreach ($usuarios as $usuario) {
        if ($usuario['id'] == $identificador || $usuario['username'] == $identificador) {
            return $usuario;
        }
    }
    
    return null;
}

// Função para adicionar saldo ao usuário
function adicionarSaldoUsuario($username, $valor) {
    $usuarios = carregarUsuarios();
    $atualizado = false;
    
    foreach ($usuarios as &$user) {
        if ($user['username'] === $username) {
            $user['saldo'] += $valor;
            
            // Atualizar o saldo na sessão se o usuário logado for o mesmo que recebe o saldo
            if (isset($_SESSION['username']) && $_SESSION['username'] === $username) {
                $_SESSION['saldo'] = $user['saldo'];
            }
            
            $atualizado = true;
            break;
        }
    }
    
    if ($atualizado) {
        return salvarUsuarios($usuarios);
    }
    
    return false;
}

// Função para descontar saldo do usuário
function descontarSaldoUsuario($username, $valor) {
    $usuarios = carregarUsuarios();
    $atualizado = false;
    
    foreach ($usuarios as &$user) {
        if ($user['username'] === $username) {
            // Verificar se tem saldo suficiente
            if ($user['saldo'] >= $valor) {
                $user['saldo'] -= $valor;
                
                // Atualizar o saldo na sessão se o usuário logado for o mesmo
                if (isset($_SESSION['username']) && $_SESSION['username'] === $username) {
                    $_SESSION['saldo'] = $user['saldo'];
                }
                
                $atualizado = true;
            }
            break;
        }
    }
    
    if ($atualizado) {
        return salvarUsuarios($usuarios);
    }
    
    return false;
}

// Garantir que o arquivo de usuários existe
if (!file_exists(ARQUIVO_USUARIOS)) {
    inicializarUsuarios();
}
?>
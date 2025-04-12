<?php
// Usuários do sistema (em produção deveriam estar em um banco de dados)
$usuarios = [
    [
        'username' => 'admin',
        // Hash fixo para 'admin123' - gerado uma única vez com password_hash
        'password' => '$2y$10$r69Q.qAH.UU4yLnJTSZ4COLyrAmu.ay7rDKKasVgmmBQtomoIlraW',
        'saldo' => 100000.00
    ],
    [
        'username' => 'user',
        // Hash fixo para 'user123' - gerado uma única vez com password_hash
        'password' => '$2y$10$ZGpiA/cmP.u4zE3mhJ.H2.nKBdyym2xYXpcLr.WshhCDb8vCvq0rq',
        'saldo' => 25000.00
    ]
];

// Função para buscar usuário pelo nome
function buscarUsuario($username) {
    global $usuarios;
    foreach ($usuarios as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}
?>
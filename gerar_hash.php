<?php
// Utilitário para gerar hashes de senha
// Execute este script uma vez para obter os hashes corretos

$senhas = [
    'admin' => 'admin123',
    'user' => 'user123'
];

echo "Hashes gerados para uso no arquivo usuarios.php:\n\n";

foreach ($senhas as $usuario => $senha) {
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    echo "Usuário: $usuario\n";
    echo "Senha: $senha\n";
    echo "Hash gerado: $hash\n\n";
}

echo "Copie estes hashes para o arquivo usuarios.php\n";
?>
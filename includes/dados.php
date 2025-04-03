<!-- Arquivo com os arrays de dados do sistema -->

<?php 

//Array com os carros para leilão (contendo arryas associativos)
$carros = [
    [
        'id' => 1,
        'titulo' => "Volksvagem Fusca 1968",
        'ano' => "1968",
        'marca' => "Volksvagem",
        'modelo' => "Fusca",
        'categoria' => "Lata velha"
    ],
    [
        'id' => 2,
        'titulo' => "Chevrolet Chevette 1991",
        'ano' => "1991",
        'marca' => "Chevorlet",
        'modelo' => "Chevette",
        'categoria' => "Tração traseira"
    ]
];

//Array simples de categorias para os filtros
$categorias = [
    'Lata velha',
    'Tração traseira',
];

//Array simples de marcas para os filtros
$marcas = [
    'Volksvagem',
    'Chevrotlet',
];

//Array de usuários, no caso, apenas um usuário
$usuarios = [
    [
        'username' => '', //Ainda não temos
        'password' => '', //Também não temos
        'nome' => ' ', //Adivinha
    ]
];

//Depois vou construir uma função para adicionar os carros

?>
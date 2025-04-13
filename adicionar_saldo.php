<?php
session_start();
include_once 'dados.php';
include_once 'funcoes.php';
include_once 'usuarios.php';

// Verificar se o usuário está logado
verificarLogin();

$mensagem = '';
$tipo_mensagem = '';

// Processar formulário de adição de saldo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_saldo'])) {
    $valor = isset($_POST['valor']) ? floatval($_POST['valor']) : 0;
    
    if ($valor <= 0) {
        $mensagem = 'Por favor, informe um valor válido maior que zero.';
        $tipo_mensagem = 'erro';
    } else {
        // Adicionar saldo ao usuário
        $resultado = adicionarSaldoUsuario($_SESSION['username'], $valor);
        
        if ($resultado) {
            $mensagem = 'Saldo adicionado com sucesso!';
            $tipo_mensagem = 'sucesso';
            
            // Atualizar o saldo na sessão (versão corrigida)
            $usuario_atualizado = buscarUsuario($_SESSION['username']);
            $_SESSION['saldo'] = $usuario_atualizado['saldo'];
        } else {
            $mensagem = 'Erro ao adicionar saldo. Tente novamente.';
            $tipo_mensagem = 'erro';
        }
    }
}

// Buscar saldo atual do usuário
$usuario = buscarUsuario($_SESSION['username']);
$saldo_atual = isset($usuario['saldo']) ? $usuario['saldo'] : 0;

include_once 'header.php';
?>

<link rel="stylesheet" href="./css/adicionar_saldo.css">

<div class="container">
    <div class="saldo-container">
        <h2>Adicionar Saldo à Conta</h2>
        
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem <?php echo $tipo_mensagem; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>
        
        <div class="saldo-atual">
            <h3>Seu saldo atual:</h3>
            <p class="valor-saldo">R$ <?php echo number_format($saldo_atual, 2, ',', '.'); ?></p>
        </div>
        
        <form method="post" action="adicionar_saldo.php">
            <div class="form-group">
                <label for="valor">Valor a adicionar (R$):</label>
                <input type="number" id="valor" name="valor" min="1" step="0.01" required>
            </div>
            
            <button type="submit" name="adicionar_saldo" class="btn btn-primary">Adicionar Saldo</button>
        </form>
        
        <div class="links-navegacao">
            <a href="protegido.php" class="btn btn-secondary">Voltar para Área Protegida</a>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
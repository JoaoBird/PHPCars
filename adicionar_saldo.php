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
            
            // Atualizar o saldo na sessão
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

<link rel="stylesheet" href="./css/adicionar_saldo.css?v=<?php echo time(); ?>">
<div class="container">
    <!-- Elementos decorativos para o container principal -->
    <div class="decoration decoration-1"></div>
    <div class="decoration decoration-2"></div>
    
    <div class="saldo-container">
        <!-- Elementos decorativos internos -->
        <div class="decoration decoration-1"></div>
        <div class="decoration decoration-2"></div>
        
        <h2>Adicionar Saldo à Conta</h2>
        
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem <?php echo $tipo_mensagem; ?>">
                <i class="icon <?php echo $tipo_mensagem; ?>-icon"></i>
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <div class="saldo-atual">
            <h3>Seu saldo atual:</h3>
            <p class="valor-saldo">R$ <?php echo number_format($saldo_atual, 2, ',', '.'); ?></p>
        </div>
        
        <form method="post" action="adicionar_saldo.php">
            <div class="form-group">
                <label for="valor">Valor a adicionar (R$):</label>
                <input type="number" id="valor" name="valor" min="1" step="0.01" required placeholder="Digite o valor desejado">
            </div>
            
            <button type="submit" name="adicionar_saldo">Adicionar Saldo</button>
        </form>
        
        <div class="links-navegacao">
            <a href="protegido.php" class="btn-secondary">Voltar para Área Protegida</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.querySelector('input[type="number"]');
    
    // Adiciona classe quando o campo ganha foco
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    // Remove classe quando o campo perde foco e está vazio
    input.addEventListener('blur', function() {
        if (this.value === '') {
            this.parentElement.classList.remove('focused');
        }
    });
    
    // Verifica se o campo já tem valor ao carregar
    if (input.value !== '') {
        input.parentElement.classList.add('focused');
    }
    
    // Adiciona animação para o botão
    const button = document.querySelector('button[type="submit"]');
    button.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
    });
    
    button.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
    
    // Animação para o valor do saldo
    const valorSaldo = document.querySelector('.valor-saldo');
    if (valorSaldo) {
        let valor = parseFloat(valorSaldo.innerText.replace('R$ ', '').replace('.', '').replace(',', '.'));
        if (!isNaN(valor) && valor > 0) {
            valorSaldo.classList.add('valor-positivo');
        }
    }
});
</script>

<?php include_once 'footer.php'; ?>
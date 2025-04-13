<?php
// leiloes.php - Gerenciador de leilões

// Carregar usuários
include_once 'usuarios.php';

// Classe para gerenciar os leilões
class LeilaoManager {
    // Nomes fictícios para gerar lances aleatórios
    private $nomes_ficticios = [
        'colecionador', 'usuario123', 'carfan87', 'classiclover', 'velocidade_maxima',
        'antigo_garage', 'rodas_retro', 'maquina_tempo', 'amante_classicos', 'piloto_vintage',
        'motor_turbo', 'drift_king', 'custom_racer', 'pneu_queimado', 'ferrugem_nao'
    ];
    
    // Obter detalhes do leilão pelo ID do carro
    public function obterLeilao($carro_id) {
        // Verificar se o leilão existe na sessão
        if (!isset($_SESSION['leiloes']) || !isset($_SESSION['leiloes'][$carro_id]) || !is_array($_SESSION['leiloes'][$carro_id])) {
            // Criar um novo leilão para este carro
            return $this->criarNovoLeilao($carro_id);
        }
        
        return $_SESSION['leiloes'][$carro_id];
    }
    
    // Criar um novo leilão
// Criar um novo leilão
private function criarNovoLeilao($carro_id) {
    global $carros;
    
    // Encontrar o carro
    $carro = null;
    foreach ($carros as $c) {
        if ($c['id'] == $carro_id) {
            $carro = $c;
            break;
        }
    }
    
    if (!$carro) {
        // Retornar um array vazio ou estrutura básica em vez de false
        return [
            'carro_id' => $carro_id,
            'preco_inicial' => 0,
            'preco_atual' => 0,
            'incremento_minimo' => 100,
            'data_inicio' => time(),
            'data_fim' => time() + (72 * 60 * 60),
            'lances' => [],
            'vencedor' => null
        ];
    }
    
    // Definir prazo de 72 horas (em segundos)
    $prazo_segundos = 72 * 60 * 60;
    
    // Criar leilão
    $leilao = [
        'carro_id' => $carro_id,
        'preco_inicial' => $carro['preco'],
        'preco_atual' => $carro['preco'],
        'incremento_minimo' => $this->calcularIncrementoMinimo($carro['preco']),
        'data_inicio' => time(),
        'data_fim' => time() + $prazo_segundos,
        'lances' => [],
        'vencedor' => null
    ];
    
    // Gerar alguns lances aleatórios iniciais para movimentar o leilão
    $this->gerarLancesIniciais($leilao);
    
    // Salvar na sessão
    if (!isset($_SESSION['leiloes'])) {
        $_SESSION['leiloes'] = [];
    }
    
    $_SESSION['leiloes'][$carro_id] = $leilao;
    
    return $leilao;
}
    
    // Calcular incremento mínimo baseado no preço (regra de negócio)
    private function calcularIncrementoMinimo($preco) {
        if ($preco < 5000) {
            return 100; // R$ 100,00 para carros até R$ 5.000,00
        } else if ($preco < 20000) {
            return 200; // R$ 200,00 para carros até R$ 20.000,00
        } else if ($preco < 50000) {
            return 500; // R$ 500,00 para carros até R$ 50.000,00
        } else if ($preco < 100000) {
            return 1000; // R$ 1.000,00 para carros até R$ 100.000,00
        } else {
            return 2000; // R$ 2.000,00 para carros acima de R$ 100.000,00
        }
    }
    
    // Gerar lances aleatórios iniciais para dar movimento ao leilão
    private function gerarLancesIniciais(&$leilao) {
        // Número aleatório de lances iniciais (1 a 5)
        $num_lances = rand(1, 5);
        
        // Preço atual para acompanhar os incrementos
        $preco_atual = $leilao['preco_inicial'];
        
        // Data inicial (entre 1 e 24 horas atrás)
        $data_base = time() - rand(3600, 86400);
        
        for ($i = 0; $i < $num_lances; $i++) {
            // Escolher um nome aleatório
            $usuario = $this->nomes_ficticios[array_rand($this->nomes_ficticios)];
            
            // Calcular incremento para este lance (entre 1x e 2x o incremento mínimo)
            $incremento = $leilao['incremento_minimo'] * (1 + (rand(0, 100) / 100));
            $preco_atual += $incremento;
            
            // Data deste lance (incrementa entre 15min e 2h da data do lance anterior)
            $data_lance = $data_base + rand(900, 7200);
            $data_base = $data_lance;
            
            // Adicionar o lance
            $leilao['lances'][] = [
                'usuario' => $usuario,
                'valor' => $preco_atual,
                'data' => $data_lance
            ];
        }
        
        // Atualizar o preço atual se houve lances
        if ($num_lances > 0) {
            $leilao['preco_atual'] = $preco_atual;
        }
    }
    
    // Registrar um novo lance

        public function registrarLance($carro_id, $usuario, $valor) {
            // Obter leilão
            $leilao = $this->obterLeilao($carro_id);
            
            // Validações básicas
            if (!$leilao) {
                return ['status' => 'erro', 'mensagem' => 'Leilão não encontrado'];
            }
            
            if (time() > $leilao['data_fim']) {
                return ['status' => 'erro', 'mensagem' => 'Este leilão já foi encerrado'];
            }
            
            if ($valor <= $leilao['preco_atual']) {
                return ['status' => 'erro', 'mensagem' => 'O lance deve ser maior que o lance atual (R$ ' . number_format($leilao['preco_atual'], 2, ',', '.') . ')'];
            }
            
            $incremento = $valor - $leilao['preco_atual'];
            if ($incremento < $leilao['incremento_minimo']) {
                return ['status' => 'erro', 'mensagem' => 'O incremento mínimo é de R$ ' . number_format($leilao['incremento_minimo'], 2, ',', '.') . ''];
            }
            
            // Verificar se o usuário tem saldo suficiente
            $usuario_obj = buscarUsuario($usuario);
            $incremento = $valor - $leilao['preco_atual'];

            // Depuração temporária
            error_log("Debug: Usuário: {$usuario}, Saldo: {$usuario_obj['saldo']}, Incremento necessário: {$incremento}");

            if (!$usuario_obj || $usuario_obj['saldo'] < $incremento) {
                return ['status' => 'erro', 'mensagem' => 'Saldo insuficiente para realizar este lance'];
            }
                                    
            // Registrar o lance
            $leilao['lances'][] = [
                'usuario' => $usuario,
                'valor' => $valor,
                'data' => time(),
                'usuario_real' => true // Marcador para identificar que é um usuário real
            ];
            
            // Atualizar preço atual
            $leilao['preco_atual'] = $valor;
            
            // Atualizar no registro de leilões
            $_SESSION['leiloes'][$carro_id] = $leilao;
            
            // Registrar timestamp do último lance do usuário
            if (!isset($_SESSION['ultimo_lance'])) {
                $_SESSION['ultimo_lance'] = [];
            }
            $_SESSION['ultimo_lance'][$usuario] = time();
            
            // Agendar lances automáticos futuros
            $this->agendarLanceFuturo($carro_id);
            
            return ['status' => 'sucesso', 'mensagem' => 'Lance registrado com sucesso!'];
        }
    
    // Verificar se o usuário pode dar lance agora (para evitar spam)
    public function podeUsuarioDarLance($usuario) {
        // Verificar se o usuário deu um lance recentemente
        if (isset($_SESSION['ultimo_lance']) && isset($_SESSION['ultimo_lance'][$usuario])) {
            $ultimo_lance = $_SESSION['ultimo_lance'][$usuario];
            $tempo_decorrido = time() - $ultimo_lance;
            
            // Deve esperar pelo menos 30 segundos entre lances
            if ($tempo_decorrido < 30) {
                return false;
            }
        }
        
        return true;
    }
    
    // Agendar um lance automático no futuro (entre 15 min e 2 horas)
    private function agendarLanceFuturo($carro_id) {
        // Na vida real, isso seria feito com um job scheduler ou cron
        // Para simulação, vamos apenas armazenar o próximo horário de lance na sessão
        $tempo_espera = rand(900, 7200); // Entre 15 min e 2 horas
        
        if (!isset($_SESSION['proximos_lances'])) {
            $_SESSION['proximos_lances'] = [];
        }
        
        $_SESSION['proximos_lances'][$carro_id] = time() + $tempo_espera;
    }
    
    // Processar lances automáticos pendentes
    public function processarLancesAutomaticos() {
        if (!isset($_SESSION['proximos_lances']) || !isset($_SESSION['leiloes'])) {
            return;
        }
        
        $agora = time();
        
        foreach ($_SESSION['proximos_lances'] as $carro_id => $horario) {
            // Verificar se chegou a hora de processar o lance
            if ($agora >= $horario) {
                // Obter o leilão
                if (isset($_SESSION['leiloes'][$carro_id])) {
                    $leilao = $_SESSION['leiloes'][$carro_id];
                    
                    // Verificar se o leilão ainda está ativo
                    if ($agora < $leilao['data_fim']) {
                        // Gerar lance automático
                        $this->gerarLanceAutomatico($carro_id);
                        
                        // Agendar próximo lance
                        $this->agendarLanceFuturo($carro_id);
                    }
                }
                
                // Remover este lance da fila
                unset($_SESSION['proximos_lances'][$carro_id]);
            }
        }
    }
    
    // Gerar um lance automático
    private function gerarLanceAutomatico($carro_id) {
        // Obter leilão
        $leilao = $this->obterLeilao($carro_id);
        
        if (!$leilao) {
            return false;
        }
        
        // Escolher um nome aleatório
        $usuario = $this->nomes_ficticios[array_rand($this->nomes_ficticios)];
        
        // Calcular valor do lance (entre 1x e 1.5x o incremento mínimo)
        $incremento = $leilao['incremento_minimo'] * (1 + (rand(0, 50) / 100));
        $valor = $leilao['preco_atual'] + $incremento;
        
        // Registrar o lance
        $leilao['lances'][] = [
            'usuario' => $usuario,
            'valor' => $valor,
            'data' => time()
        ];
        
        // Atualizar preço atual
        $leilao['preco_atual'] = $valor;
        
        // Atualizar no registro de leilões
        $_SESSION['leiloes'][$carro_id] = $leilao;
        
        return true;
    }
    
    // Verificar leilões finalizados e definir vencedores
    public function verificarLeiloesFinalizados() {
        if (!isset($_SESSION['leiloes'])) {
            return;
        }
        
        $agora = time();
        
        foreach ($_SESSION['leiloes'] as $carro_id => $leilao) {
            // Verificar se o leilão já terminou e não tem vencedor definido
            if ($agora >= $leilao['data_fim'] && $leilao['vencedor'] === null) {
                // Verificar se houve lances
                if (count($leilao['lances']) > 0) {
                    // O último lance é o vencedor
                    $ultimo_lance = end($leilao['lances']);
                    $leilao['vencedor'] = $ultimo_lance['usuario'];
                    
                    // Atualizar leilão
                    $_SESSION['leiloes'][$carro_id] = $leilao;
                }
            }
        }
    }
    
    // Formatar tempo restante de forma amigável
    public function formatarTempoRestante($data_fim) {
        $agora = time();
        $tempo_restante = $data_fim - $agora;
        
        if ($tempo_restante <= 0) {
            return "Encerrado";
        }
        
        $dias = floor($tempo_restante / 86400);
        $horas = floor(($tempo_restante % 86400) / 3600);
        $minutos = floor(($tempo_restante % 3600) / 60);
        
        if ($dias > 0) {
            return "$dias dias, $horas horas";
        } else if ($horas > 0) {
            return "$horas horas, $minutos minutos";
        } else {
            return "$minutos minutos";
        }
    }
}

// Instanciar o gerenciador de leilões
$leilaoManager = new LeilaoManager();

// Processar lances automáticos e verificar leilões finalizados
// (chamamos isso em todas as requisições para simular um processamento em background)
$leilaoManager->processarLancesAutomaticos();
$leilaoManager->verificarLeiloesFinalizados();
?>
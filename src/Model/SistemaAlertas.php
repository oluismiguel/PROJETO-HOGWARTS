<?php
namespace Src\Model;

use Carbon\Carbon;

class SistemaAlertas
{
    private const COR_TITULO = "\033[1;36m";
    private const COR_OPCAO = "\033[1;33m";
    private const COR_DESTAQUE = "\033[1;35m";
    private const COR_RESET = "\033[0m";
    private const COR_ERRO = "\033[1;31m";
    private const COR_SUCESSO = "\033[1;32m";
    private const COR_NOTIFICACAO = "\033[1;34m";
    private const COR_URGENTE = "\033[1;31m";
    private const COR_AGENDADA = "\033[1;33m";

    private array $notificacoes = [];
    private string $usuarioAtual;
    private string $tipoUsuario; // 'aluno', 'professor', 'admin'
    private string $casaUsuario; // 'Grifinória', 'Sonserina', etc.

    public function __construct(string $usuarioAtual, string $tipoUsuario, string $casaUsuario = '')
    {
        $this->usuarioAtual = $usuarioAtual;
        $this->tipoUsuario = $tipoUsuario;
        $this->casaUsuario = $casaUsuario;
        $this->carregarNotificacoes();
    }

    public function executar(): void
    {
        while (true) {
            $this->limparTela();
            $this->exibirCabecalho();
            $this->exibirNotificacoesPendentes();
            $this->exibirMenu();
            
            $opcao = $this->lerOpcao();
            
            if ($opcao === '0') {
                break;
            }
            
            $this->processarOpcao($opcao);
        }
    }

    private function limparTela(): void
    {
        echo "\033c";
    }

    private function exibirCabecalho(): void
    {
        echo self::COR_TITULO . "=============================================\n";
        echo "      SISTEMA DE ALERTAS DE HOGWARTS\n";
        echo "=============================================\n\n" . self::COR_RESET;
        echo "Usuário: " . self::COR_DESTAQUE . $this->usuarioAtual . self::COR_RESET;
        echo " | Tipo: " . self::COR_DESTAQUE . $this->tipoUsuario . self::COR_RESET;
        if ($this->casaUsuario) {
            echo " | Casa: " . self::COR_DESTAQUE . $this->casaUsuario . self::COR_RESET;
        }
        echo "\n\n";
    }

    private function exibirNotificacoesPendentes(): void
    {
        $notificacoes = $this->filtrarNotificacoesParaUsuario();
        
        if (empty($notificacoes)) {
            echo self::COR_SUCESSO . "Nenhuma notificação pendente.\n\n" . self::COR_RESET;
            return;
        }

        echo self::COR_DESTAQUE . "NOTIFICAÇÕES RECENTES:\n" . self::COR_RESET;
        
        foreach ($notificacoes as $notificacao) {
            $cor = $notificacao['prioridade'] === 'urgente' ? self::COR_URGENTE : 
                  ($notificacao['prioridade'] === 'agendada' ? self::COR_AGENDADA : self::COR_NOTIFICACAO);
            
            echo $cor . "[" . $notificacao['data'] . "] " . $notificacao['remetente'] . ":\n";
            echo "> " . $notificacao['mensagem'] . "\n\n" . self::COR_RESET;
        }
    }

    private function filtrarNotificacoesParaUsuario(bool $incluirLidas = false): array
    {
        $notificacoesUsuario = [];
        
        foreach ($this->notificacoes as $notificacao) {
            $destinoValido = $notificacao['destinatario'] === 'todos' ||
                            $notificacao['destinatario'] === $this->tipoUsuario ||
                            $notificacao['destinatario'] === $this->casaUsuario ||
                            $notificacao['destinatario'] === $this->usuarioAtual;
            
            if ($destinoValido && ($incluirLidas || !$notificacao['lida'])) {
                $notificacoesUsuario[] = $notificacao;
            }
        }
        
        // Ordena por data (mais recente primeiro)
        usort($notificacoesUsuario, function ($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });
        
        return $notificacoesUsuario;
    }

    private function exibirMenu(): void
    {
        echo self::COR_DESTAQUE . "MENU DE ALERTAS:\n" . self::COR_RESET;
        
        $opcoes = [
            '1' => 'Enviar notificação',
            '2' => 'Ver histórico de notificações',
            '3' => 'Agendar notificação (admin)',
            '0' => 'Voltar ao menu principal'
        ];
        
        // Ajusta opções disponíveis baseado no tipo de usuário
        if ($this->tipoUsuario !== 'admin') {
            unset($opcoes['3']);
        }
        if ($this->tipoUsuario === 'aluno') {
            $opcoes['1'] = 'Enviar mensagem para professor';
        }
        
        foreach ($opcoes as $key => $opcao) {
            echo "  " . self::COR_OPCAO . "[{$key}]" . self::COR_RESET . " {$opcao}\n";
        }
        
        echo "\n";
    }

    private function lerOpcao(): string
    {
        echo self::COR_DESTAQUE . "Digite sua opção: " . self::COR_RESET;
        return trim(fgets(STDIN));
    }

    private function processarOpcao(string $opcao): void
    {
        switch ($opcao) {
            case '1':
                $this->enviarNotificacao();
                break;
            case '2':
                $this->verHistoricoNotificacoes();
                break;
            case '3':
                if ($this->tipoUsuario === 'admin') {
                    $this->agendarNotificacao();
                } else {
                    $this->exibirMensagemErro("Opção inválida!");
                }
                break;
            default:
                $this->exibirMensagemErro("Opção inválida!");
        }
    }

    private function enviarNotificacao(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== ENVIAR NOTIFICAÇÃO ===\n\n" . self::COR_RESET;
        
        // Determina opções de destinatário baseado no tipo de usuário
        $opcoesDestino = [];
        
        if ($this->tipoUsuario === 'professor') {
            $opcoesDestino = [
                '1' => ['desc' => 'Turma específica', 'valor' => 'turma'],
                '2' => ['desc' => 'Todos os alunos', 'valor' => 'alunos'],
                '3' => ['desc' => 'Todos os professores', 'valor' => 'professores'],
                '4' => ['desc' => 'Toda a escola', 'valor' => 'todos']
            ];
        } elseif ($this->tipoUsuario === 'admin') {
            $opcoesDestino = [
                '1' => ['desc' => 'Casa específica', 'valor' => 'casa'],
                '2' => ['desc' => 'Todos os alunos', 'valor' => 'alunos'],
                '3' => ['desc' => 'Todos os professores', 'valor' => 'professores'],
                '4' => ['desc' => 'Toda a escola', 'valor' => 'todos']
            ];
        } else { // aluno
            $opcoesDestino = [
                '1' => ['desc' => 'Professor específico', 'valor' => 'professor'],
                '2' => ['desc' => 'Diretor', 'valor' => 'admin']
            ];
        }
        
        // Exibe opções de destino
        echo "Destinatário:\n";
        foreach ($opcoesDestino as $key => $opcao) {
            echo "  " . self::COR_OPCAO . "[{$key}]" . self::COR_RESET . " {$opcao['desc']}\n";
        }
        echo "\n";
        
        $opcaoDestino = $this->lerOpcao();
        
        if (!isset($opcoesDestino[$opcaoDestino])) {
            $this->exibirMensagemErro("Opção inválida!");
            return;
        }
        
        $tipoDestino = $opcoesDestino[$opcaoDestino]['valor'];
        $destinatario = '';
        
        // Tratamento específico para cada tipo de destino
        switch ($tipoDestino) {
            case 'turma':
                echo "Digite o nome da turma: ";
                $turma = trim(fgets(STDIN));
                $destinatario = "turma:$turma";
                break;
            case 'casa':
                echo "Digite o nome da casa (Grifinória, Sonserina, etc.): ";
                $casa = trim(fgets(STDIN));
                $destinatario = $casa;
                break;
            case 'professor':
                echo "Digite o nome do professor: ";
                $professor = trim(fgets(STDIN));
                $destinatario = $professor;
                break;
            default:
                $destinatario = $tipoDestino;
        }
        
        // Prioridade
        $prioridades = [
            '1' => 'normal',
            '2' => 'urgente'
        ];
        
        echo "\nPrioridade:\n";
        echo "  " . self::COR_OPCAO . "[1]" . self::COR_RESET . " Normal\n";
        echo "  " . self::COR_OPCAO . "[2]" . self::COR_RESET . " Urgente\n";
        echo "\n";
        
        $opcaoPrioridade = $this->lerOpcao();
        $prioridade = $prioridades[$opcaoPrioridade] ?? 'normal';
        
        // Mensagem
        echo "\nDigite sua mensagem (max 500 caracteres):\n";
        $mensagem = trim(fgets(STDIN));
        $mensagem = substr($mensagem, 0, 500);
        
        // Confirmação
        echo "\n" . self::COR_DESTAQUE . "Resumo da notificação:\n" . self::COR_RESET;
        echo "Destinatário: " . self::COR_OPCAO . $destinatario . self::COR_RESET . "\n";
        echo "Prioridade: " . self::COR_OPCAO . $prioridade . self::COR_RESET . "\n";
        echo "Mensagem: " . self::COR_OPCAO . $mensagem . self::COR_RESET . "\n\n";
        echo "Confirmar envio? (s/n): ";
        $confirmacao = trim(fgets(STDIN));
        
        if (strtolower($confirmacao) === 's') {
            $this->adicionarNotificacao($this->usuarioAtual, $destinatario, $mensagem, $prioridade);
            $this->exibirMensagemSucesso("Notificação enviada com sucesso!");
        } else {
            $this->exibirMensagemSucesso("Envio cancelado.");
        }
    }

    private function agendarNotificacao(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== AGENDAR NOTIFICAÇÃO ===\n\n" . self::COR_RESET;
        
        echo "Esta função permite agendar notificações para envio futuro.\n\n";
        
        // Destinatário
        $opcoesDestino = [
            '1' => ['desc' => 'Casa específica', 'valor' => 'casa'],
            '2' => ['desc' => 'Todos os alunos', 'valor' => 'alunos'],
            '3' => ['desc' => 'Todos os professores', 'valor' => 'professores'],
            '4' => ['desc' => 'Toda a escola', 'valor' => 'todos']
        ];
        
        echo "Destinatário:\n";
        foreach ($opcoesDestino as $key => $opcao) {
            echo "  " . self::COR_OPCAO . "[{$key}]" . self::COR_RESET . " {$opcao['desc']}\n";
        }
        echo "\n";
        
        $opcaoDestino = $this->lerOpcao();
        
        if (!isset($opcoesDestino[$opcaoDestino])) {
            $this->exibirMensagemErro("Opção inválida!");
            return;
        }
        
        $tipoDestino = $opcoesDestino[$opcaoDestino]['valor'];
        $destinatario = '';
        
        if ($tipoDestino === 'casa') {
            echo "Digite o nome da casa (Grifinória, Sonserina, etc.): ";
            $casa = trim(fgets(STDIN));
            $destinatario = $casa;
        } else {
            $destinatario = $tipoDestino;
        }
        
        // Data e hora
        echo "\nDigite a data e hora para envio (formato DD/MM/AAAA HH:MM): ";
        $dataHora = trim(fgets(STDIN));
        
        try {
            $dataAgendamento = Carbon::createFromFormat('d/m/Y H:i', $dataHora);
            
            if ($dataAgendamento->isPast()) {
                $this->exibirMensagemErro("A data deve ser futura!");
                return;
            }
        } catch (\Exception $e) {
            $this->exibirMensagemErro("Formato de data inválido!");
            return;
        }
        
        // Mensagem
        echo "\nDigite sua mensagem (max 500 caracteres):\n";
        $mensagem = trim(fgets(STDIN));
        $mensagem = substr($mensagem, 0, 500);
        
        // Confirmação
        echo "\n" . self::COR_DESTAQUE . "Resumo da notificação agendada:\n" . self::COR_RESET;
        echo "Destinatário: " . self::COR_OPCAO . $destinatario . self::COR_RESET . "\n";
        echo "Data/hora: " . self::COR_OPCAO . $dataAgendamento->format('d/m/Y H:i') . self::COR_RESET . "\n";
        echo "Mensagem: " . self::COR_OPCAO . $mensagem . self::COR_RESET . "\n\n";
        echo "Confirmar agendamento? (s/n): ";
        $confirmacao = trim(fgets(STDIN));
        
        if (strtolower($confirmacao) === 's') {
            $this->adicionarNotificacao(
                $this->usuarioAtual,
                $destinatario,
                $mensagem,
                'agendada',
                $dataAgendamento
            );
            $this->exibirMensagemSucesso("Notificação agendada com sucesso para " . $dataAgendamento->format('d/m/Y H:i') . "!");
        } else {
            $this->exibirMensagemSucesso("Agendamento cancelado.");
        }
    }

    private function verHistoricoNotificacoes(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== HISTÓRICO DE NOTIFICAÇÕES ===\n\n" . self::COR_RESET;
        
        $todasNotificacoes = $this->filtrarNotificacoesParaUsuario(true);
        
        if (empty($todasNotificacoes)) {
            echo self::COR_SUCESSO . "Nenhuma notificação encontrada.\n\n" . self::COR_RESET;
            echo self::COR_SUCESSO . "Pressione Enter para continuar..." . self::COR_RESET;
            fgets(STDIN);
            return;
        }
        
        foreach ($todasNotificacoes as $notificacao) {
            $cor = $notificacao['prioridade'] === 'urgente' ? self::COR_URGENTE : 
                  ($notificacao['prioridade'] === 'agendada' ? self::COR_AGENDADA : self::COR_NOTIFICACAO);
            
            $status = $notificacao['lida'] ? '(lida)' : '(não lida)';
            
            echo $cor . "[" . $notificacao['data'] . "] " . $notificacao['remetente'] . " {$status}:\n";
            echo "> " . $notificacao['mensagem'] . "\n\n" . self::COR_RESET;
        }
        
        echo self::COR_SUCESSO . "Pressione Enter para continuar..." . self::COR_RESET;
        fgets(STDIN);
    }

    private function adicionarNotificacao(
        string $remetente,
        string $destinatario,
        string $mensagem,
        string $prioridade = 'normal',
        ?Carbon $dataAgendamento = null
    ): void {
        $novaNotificacao = [
            'id' => uniqid(),
            'remetente' => $remetente,
            'destinatario' => $destinatario,
            'mensagem' => $mensagem,
            'prioridade' => $prioridade,
            'data' => $dataAgendamento ? $dataAgendamento->format('d/m/Y H:i') : date('d/m/Y H:i'),
            'dataEnvio' => $dataAgendamento ? $dataAgendamento->toDateTimeString() : Carbon::now()->toDateTimeString(),
            'lida' => false,
            'agendada' => $dataAgendamento !== null
        ];
        
        $this->notificacoes[] = $novaNotificacao;
        $this->salvarNotificacoes();
    }

    private function carregarNotificacoes(): void
    {
        // Notificações de exemplo
        $this->notificacoes = [
            [
                'id' => '1',
                'remetente' => 'Diretor Dumbledore',
                'destinatario' => 'todos',
                'mensagem' => 'Bem-vindos ao novo ano letivo! Lembrem-se: as Florestas Proibidas estão proibidas por um motivo.',
                'prioridade' => 'normal',
                'data' => date('d/m/Y H:i', strtotime('-2 days')),
                'dataEnvio' => Carbon::now()->subDays(2)->toDateTimeString(),
                'lida' => false,
                'agendada' => false
            ],
            [
                'id' => '2',
                'remetente' => 'Prof. Minerva McGonagall',
                'destinatario' => 'alunos',
                'mensagem' => 'Aula de Transfiguração cancelada amanhã. Estudem o capítulo 5 para a próxima aula.',
                'prioridade' => 'urgente',
                'data' => date('d/m/Y H:i', strtotime('-1 day')),
                'dataEnvio' => Carbon::now()->subDay()->toDateTimeString(),
                'lida' => false,
                'agendada' => false
            ],
            [
                'id' => '3',
                'remetente' => 'Sistema',
                'destinatario' => 'todos',
                'mensagem' => 'Lembretes: Torneio de Quadribol neste sábado. Todos são bem-vindos!',
                'prioridade' => 'normal',
                'data' => date('d/m/Y H:i'),
                'dataEnvio' => Carbon::now()->toDateTimeString(),
                'lida' => false,
                'agendada' => false
            ]
        ];
    }

    private function salvarNotificacoes(): void
    {
        // Em um sistema real, isso salvaria no banco de dados
    }

    private function exibirMensagemSucesso(string $mensagem): void
    {
        echo "\n" . self::COR_SUCESSO . $mensagem . self::COR_RESET . "\n";
        sleep(2);
    }

    private function exibirMensagemErro(string $mensagem): void
    {
        echo "\n" . self::COR_ERRO . $mensagem . self::COR_RESET . "\n";
        sleep(2);
    }
}

// Atualização da classe MenuPrincipal para incluir o módulo 6
class MenuPrincipal
{
    private const COR_TITULO = "\033[1;36m";
    private const COR_OPCAO = "\033[1;33m";
    private const COR_DESTAQUE = "\033[1;35m";
    private const COR_RESET = "\033[0m";
    private const COR_ERRO = "\033[1;31m";
    private const COR_SUCESSO = "\033[1;32m";

    private array $modulos = [
        '1' => 'Convite e Cadastro de Alunos',
        '2' => 'Seleção de Casas',
        '3' => 'Gerenciamento de Torneios',
        '4' => 'Controle Acadêmico e Disciplinar',
        '5' => 'Gerenciamento de Professores e Funcionários',
        '6' => 'Sistema de Alertas',
        '0' => 'Sair'
    ];

    public function executar(): void
    {
        while (true) {
            $this->limparTela();
            $this->exibirCabecalho();
            $this->exibirOpcoes();
            
            $opcao = $this->lerOpcao();
            
            if ($opcao === '0') {
                $this->exibirMensagemDespedida();
                break;
            }
            
            $this->redirecionarParaModulo($opcao);
        }
    }

    private function limparTela(): void
    {
        echo "\033c";
    }

    private function exibirCabecalho(): void
    {
        echo self::COR_TITULO . "=============================================\n";
        echo "      SISTEMA DE GESTÃO DE HOGWARTS\n";
        echo "=============================================\n\n" . self::COR_RESET;
    }

    private function exibirOpcoes(): void
    {
        echo self::COR_DESTAQUE . "MENU PRINCIPAL:\n" . self::COR_RESET;
        
        foreach ($this->modulos as $key => $modulo) {
            echo "  " . self::COR_OPCAO . "[{$key}]" . self::COR_RESET . " {$modulo}\n";
        }
        
        echo "\n";
    }

    private function lerOpcao(): string
    {
        echo self::COR_DESTAQUE . "Digite sua opção: " . self::COR_RESET;
        return trim(fgets(STDIN));
    }

    private function redirecionarParaModulo(string $opcao): void
    {
        try {
            $this->limparTela();
            
            switch ($opcao) {
                case '1':
                    $modulo = new ConviteECadastroDeAlunos();
                    break;
                case '2':
                    $modulo = new SelecaoDeCasas();
                    break;
                case '3':
                    $modulo = new GerenciamentoDeTorneios();
                    break;
                case '4':
                    $modulo = new ControleAcademicoDisciplinar();
                    break;
                case '5':
                    $modulo = new GerenciamentoProfessores();
                    break;
                case '6':
                    // Simulando um usuário logado (Harry Potter, aluno da Grifinória)
                    $modulo = new SistemaAlertas('Harry Potter', 'aluno', 'Grifinória');
                    break;
                default:
                    echo self::COR_DESTAQUE . "Módulo em desenvolvimento.\n" . self::COR_RESET;
                    echo "\n" . self::COR_SUCESSO . "Pressione Enter para continuar..." . self::COR_RESET;
                    fgets(STDIN);
                    return;
            }
            
            echo self::COR_SUCESSO . ">>> Módulo: {$this->modulos[$opcao]}\n\n" . self::COR_RESET;
            $modulo->executar();
            
        } catch (\Exception $e) {
            echo self::COR_ERRO . "Erro: " . $e->getMessage() . "\n" . self::COR_RESET;
            sleep(2);
        }
    }

    private function exibirMensagemDespedida(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=============================================\n";
        echo "           OBRIGADO POR USAR O SISTEMA\n";
        echo "=============================================\n\n" . self::COR_RESET;
        
        $frases = [
            "Que a magia esteja com você!",
            "Até a próxima, bruxo!",
            "Hogwarts estará sempre aqui para te receber!",
            "Mischief managed!"
        ];
        
        echo self::COR_DESTAQUE . $frases[array_rand($frases)] . self::COR_RESET . "\n\n";
    }
}
<?php
namespace Src\Model;

use Src\Model\ConviteECadastroDeAlunos;
use Src\Model\SelecaoDeCasas;
use Src\Model\GerenciamentoDeTorneios;
use Src\Model\ControleAcademicoDisciplinar;
use Src\Model\GerenciamentoProfessores;
use Src\Model\SistemaAlertas;

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
                    // Inicializa o módulo de alertas com um usuário padrão
                    $modulo = new SistemaAlertas('Usuário Teste', 'admin', 'Grifinória');
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

// Classe do Módulo 6 - Sistema de Alertas
class SistemaAlertas
{
    private const COR_TITULO = "\033[1;36m";
    private const COR_OPCAO = "\033[1;33m";
    private const COR_DESTAQUE = "\033[1;35m";
    private const COR_RESET = "\033[0m";
    private const COR_ERRO = "\033[1;31m";
    private const COR_SUCESSO = "\033[1;32m";
    private const COR_NOTIFICACAO = "\033[1;34m";

    private array $notificacoes = [];
    private string $usuarioAtual;
    private string $tipoUsuario;
    private string $casaUsuario;

    public function __construct(string $usuarioAtual, string $tipoUsuario, string $casaUsuario = '')
    {
        $this->usuarioAtual = $usuarioAtual;
        $this->tipoUsuario = $tipoUsuario;
        $this->casaUsuario = $casaUsuario;
        $this->carregarNotificacoesExemplo();
    }

    public function executar(): void
    {
        while (true) {
            $this->limparTela();
            $this->exibirCabecalho();
            $this->exibirNotificacoes();
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
        if (!empty($this->casaUsuario)) {
            echo " | Casa: " . self::COR_DESTAQUE . $this->casaUsuario . self::COR_RESET;
        }
        echo "\n\n";
    }

    private function exibirNotificacoes(): void
    {
        if (empty($this->notificacoes)) {
            echo self::COR_SUCESSO . "Nenhuma notificação disponível.\n\n" . self::COR_RESET;
            return;
        }

        echo self::COR_DESTAQUE . "ÚLTIMAS NOTIFICAÇÕES:\n" . self::COR_RESET;
        
        foreach ($this->notificacoes as $notificacao) {
            echo self::COR_NOTIFICACAO . "[" . $notificacao['data'] . "] " . $notificacao['remetente'] . ":\n";
            echo "> " . $notificacao['mensagem'] . "\n\n" . self::COR_RESET;
        }
    }

    private function exibirMenu(): void
    {
        echo self::COR_DESTAQUE . "OPÇÕES DISPONÍVEIS:\n" . self::COR_RESET;
        
        $opcoes = [
            '1' => 'Enviar nova notificação',
            '2' => 'Ver histórico completo',
            '0' => 'Voltar ao menu principal'
        ];
        
        // Adiciona opção de agendamento apenas para administradores
        if ($this->tipoUsuario === 'admin') {
            $opcoes['3'] = 'Agendar notificação';
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
                $this->verHistorico();
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
        
        echo "Digite o destinatário (ou 'todos' para todos os usuários): ";
        $destinatario = trim(fgets(STDIN));
        
        echo "\nDigite a mensagem (máx. 500 caracteres):\n";
        $mensagem = substr(trim(fgets(STDIN)), 0, 500);
        
        $this->notificacoes[] = [
            'remetente' => $this->usuarioAtual,
            'destinatario' => $destinatario,
            'mensagem' => $mensagem,
            'data' => date('d/m/Y H:i'),
            'lida' => false
        ];
        
        $this->exibirMensagemSucesso("Notificação enviada com sucesso!");
    }

    private function agendarNotificacao(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== AGENDAR NOTIFICAÇÃO ===\n\n" . self::COR_RESET;
        
        echo "Digite o destinatário (ou 'todos' para todos os usuários): ";
        $destinatario = trim(fgets(STDIN));
        
        echo "\nDigite a data e hora para envio (formato DD/MM/AAAA HH:MM): ";
        $dataHora = trim(fgets(STDIN));
        
        echo "\nDigite a mensagem (máx. 500 caracteres):\n";
        $mensagem = substr(trim(fgets(STDIN)), 0, 500);
        
        $this->notificacoes[] = [
            'remetente' => $this->usuarioAtual,
            'destinatario' => $destinatario,
            'mensagem' => $mensagem,
            'data' => $dataHora,
            'lida' => false,
            'agendada' => true
        ];
        
        $this->exibirMensagemSucesso("Notificação agendada para $dataHora!");
    }

    private function verHistorico(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== HISTÓRICO DE NOTIFICAÇÕES ===\n\n" . self::COR_RESET;
        
        if (empty($this->notificacoes)) {
            echo self::COR_SUCESSO . "Nenhuma notificação no histórico.\n" . self::COR_RESET;
        } else {
            foreach ($this->notificacoes as $notificacao) {
                $status = isset($notificacao['agendada']) && $notificacao['agendada'] ? '(agendada)' : '';
                echo self::COR_NOTIFICACAO . "[" . $notificacao['data'] . "] " . $notificacao['remetente'] . " $status:\n";
                echo "> " . $notificacao['mensagem'] . "\n\n" . self::COR_RESET;
            }
        }
        
        echo self::COR_SUCESSO . "Pressione Enter para continuar..." . self::COR_RESET;
        fgets(STDIN);
    }

    private function carregarNotificacoesExemplo(): void
    {
        $this->notificacoes = [
            [
                'remetente' => 'Diretor Dumbledore',
                'destinatario' => 'todos',
                'mensagem' => 'Bem-vindos ao novo ano letivo! Lembrem-se: as Florestas Proibidas estão proibidas por um motivo.',
                'data' => '01/09/2023 08:00',
                'lida' => false
            ],
            [
                'remetente' => 'Prof. Minerva McGonagall',
                'destinatario' => 'alunos',
                'mensagem' => 'Aula de Transfiguração cancelada amanhã. Estudem o capítulo 5 para a próxima aula.',
                'data' => '15/09/2023 14:30',
                'lida' => false
            ]
        ];
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
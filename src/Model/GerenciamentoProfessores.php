<?php
namespace Src\Model;

use Exception;

class GerenciamentoProfessores
{
    private const COR_TITULO = "\033[1;36m";
    private const COR_SUCESSO = "\033[1;32m";
    private const COR_ERRO = "\033[1;31m";
    private const COR_AVISO = "\033[1;33m";
    private const COR_RESET = "\033[0m";
    private const COR_DESTAQUE = "\033[1;35m";

    private array $professores = [];
    private array $disciplinas = [];
    private array $turmas = [];
    private array $aulas = [];
    private string $arquivoDados;

    public function __construct()
    {
        $this->arquivoDados = __DIR__ . '/../../data/professores.json';
        $this->carregarDados();
        $this->carregarDadosComplementares();
    }

    public function executar(): void
    {
        while (true) {
            $this->limparTela();
            $this->exibirMenu();
            $opcao = $this->lerOpcao();

            switch ($opcao) {
                case '1':
                    $this->cadastrarProfessor();
                    break;
                case '2':
                    $this->associarDisciplina();
                    break;
                case '3':
                    $this->alocarTurma();
                    break;
                case '4':
                    $this->gerenciarCronograma();
                    break;
                case '5':
                    $this->visualizarProfessores();
                    break;
                case '0':
                    $this->salvarDados();
                    return;
                default:
                    echo self::COR_ERRO . "Opção inválida!\n" . self::COR_RESET;
                    sleep(1);
            }
        }
    }

    private function exibirMenu(): void
    {
        echo self::COR_TITULO . "=============================================\n";
        echo "      MÓDULO 5 - GERENCIAMENTO DE PROFESSORES\n";
        echo "=============================================\n\n" . self::COR_RESET;

        echo self::COR_DESTAQUE . "MENU:\n" . self::COR_RESET;
        echo "  [1] Cadastrar Novo Professor\n";
        echo "  [2] Associar a Disciplinas\n";
        echo "  [3] Alocar em Turmas\n";
        echo "  [4] Gerenciar Cronograma\n";
        echo "  [5] Visualizar Professores\n";
        echo "  [0] Voltar ao Menu Principal\n\n";
    }

    private function carregarDados(): void
    {
        if (file_exists($this->arquivoDados)) {
            $dados = file_get_contents($this->arquivoDados);
            $this->professores = json_decode($dados, true) ?? [];
        } else {
            if (!file_exists(dirname($this->arquivoDados))) {
                mkdir(dirname($this->arquivoDados), 0777, true);
            }
            $this->salvarDados();
        }
    }

    private function carregarDadosComplementares(): void
    {
        $arquivoDisciplinas = __DIR__ . '/../../data/disciplinas.json';
        if (file_exists($arquivoDisciplinas)) {
            $this->disciplinas = json_decode(file_get_contents($arquivoDisciplinas), true) ?? [];
        }

        $arquivoTurmas = __DIR__ . '/../../data/turmas.json';
        if (file_exists($arquivoTurmas)) {
            $this->turmas = json_decode(file_get_contents($arquivoTurmas), true) ?? [];
        }
    }

    private function salvarDados(): void
    {
        file_put_contents($this->arquivoDados, json_encode($this->professores, JSON_PRETTY_PRINT));
    }

    private function limparTela(): void
    {
        echo "\033c";
    }

    private function lerOpcao(): string
    {
        echo self::COR_DESTAQUE . "Digite sua opção: " . self::COR_RESET;
        return trim(fgets(STDIN));
    }

    private function cadastrarProfessor(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== CADASTRAR NOVO PROFESSOR ===\n\n" . self::COR_RESET;

        echo "Nome completo: ";
        $nome = trim(fgets(STDIN));

        echo "Especialização: ";
        $especializacao = trim(fgets(STDIN));

        echo "E-mail: ";
        $email = trim(fgets(STDIN));

        echo "Tipo (Professor/Funcionário): ";
        $tipo = trim(fgets(STDIN));

        $id = uniqid();
        $this->professores[$id] = [
            'id' => $id,
            'nome' => $nome,
            'especializacao' => $especializacao,
            'email' => $email,
            'tipo' => $tipo,
            'disciplinas' => [],
            'turmas' => [],
            'aulas' => [],
            'data_cadastro' => date('d/m/Y H:i:s')
        ];

        echo self::COR_SUCESSO . "\nProfessor cadastrado com sucesso! ID: {$id}\n" . self::COR_RESET;
        sleep(2);
    }

    private function associarDisciplina(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== ASSOCIAR PROFESSOR A DISCIPLINAS ===\n\n" . self::COR_RESET;

        if (empty($this->professores)) {
            echo self::COR_AVISO . "Nenhum professor cadastrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        if (empty($this->disciplinas)) {
            echo self::COR_AVISO . "Nenhuma disciplina cadastrada.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Listar professores
        echo "Professores disponíveis:\n";
        foreach ($this->professores as $id => $professor) {
            echo " - [$id] {$professor['nome']} ({$professor['especializacao']})\n";
        }

        echo "\nID do Professor: ";
        $professorId = trim(fgets(STDIN));

        if (!isset($this->professores[$professorId])) {
            echo self::COR_ERRO . "\nProfessor não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Listar disciplinas
        echo "\nDisciplinas disponíveis:\n";
        foreach ($this->disciplinas as $id => $disciplina) {
            echo " - [$id] {$disciplina['nome']}\n";
        }

        echo "\nID da Disciplina: ";
        $disciplinaId = trim(fgets(STDIN));

        if (!isset($this->disciplinas[$disciplinaId])) {
            echo self::COR_ERRO . "\nDisciplina não encontrada.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Associar disciplina ao professor
        if (!in_array($disciplinaId, $this->professores[$professorId]['disciplinas'])) {
            $this->professores[$professorId]['disciplinas'][] = $disciplinaId;
            echo self::COR_SUCESSO . "\nDisciplina associada com sucesso!\n" . self::COR_RESET;
        } else {
            echo self::COR_AVISO . "\nEste professor já está associado a esta disciplina.\n" . self::COR_RESET;
        }
        sleep(2);
    }

    private function alocarTurma(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== ALOCAR PROFESSOR EM TURMA ===\n\n" . self::COR_RESET;

        if (empty($this->professores)) {
            echo self::COR_AVISO . "Nenhum professor cadastrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        if (empty($this->turmas)) {
            echo self::COR_AVISO . "Nenhuma turma cadastrada.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Listar professores
        echo "Professores disponíveis:\n";
        foreach ($this->professores as $id => $professor) {
            echo " - [$id] {$professor['nome']}\n";
        }

        echo "\nID do Professor: ";
        $professorId = trim(fgets(STDIN));

        if (!isset($this->professores[$professorId])) {
            echo self::COR_ERRO . "\nProfessor não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Listar turmas
        echo "\nTurmas disponíveis:\n";
        foreach ($this->turmas as $id => $turma) {
            echo " - [$id] {$turma['nome']} (Ano: {$turma['ano']})\n";
        }

        echo "\nID da Turma: ";
        $turmaId = trim(fgets(STDIN));

        if (!isset($this->turmas[$turmaId])) {
            echo self::COR_ERRO . "\nTurma não encontrada.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Alocar professor na turma
        if (!in_array($turmaId, $this->professores[$professorId]['turmas'])) {
            $this->professores[$professorId]['turmas'][] = $turmaId;
            echo self::COR_SUCESSO . "\nProfessor alocado na turma com sucesso!\n" . self::COR_RESET;
        } else {
            echo self::COR_AVISO . "\nEste professor já está alocado nesta turma.\n" . self::COR_RESET;
        }
        sleep(2);
    }

    private function gerenciarCronograma(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== GERENCIAR CRONOGRAMA ===\n\n" . self::COR_RESET;

        if (empty($this->professores)) {
            echo self::COR_AVISO . "Nenhum professor cadastrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Listar professores
        echo "Professores disponíveis:\n";
        foreach ($this->professores as $id => $professor) {
            echo " - [$id] {$professor['nome']}\n";
        }

        echo "\nID do Professor: ";
        $professorId = trim(fgets(STDIN));

        if (!isset($this->professores[$professorId])) {
            echo self::COR_ERRO . "\nProfessor não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $professor = &$this->professores[$professorId];

        while (true) {
            $this->limparTela();
            echo self::COR_TITULO . "=== CRONOGRAMA DE {$professor['nome']} ===\n\n" . self::COR_RESET;

            if (!empty($professor['aulas'])) {
                echo "Aulas agendadas:\n";
                foreach ($professor['aulas'] as $id => $aula) {
                    $disciplina = $this->disciplinas[$aula['disciplina_id']]['nome'] ?? 'Disciplina não encontrada';
                    $turma = $this->turmas[$aula['turma_id']]['nome'] ?? 'Turma não encontrada';
                    echo " - [$id] {$disciplina} com {$turma} - {$aula['dia_semana']} às {$aula['hora']}\n";
                }
                echo "\n";
            }

            echo "Opções:\n";
            echo " [1] Agendar Nova Aula\n";
            echo " [2] Remover Aula\n";
            echo " [0] Voltar\n";
            echo "Opção: ";
            $opcao = trim(fgets(STDIN));

            switch ($opcao) {
                case '1':
                    $this->agendarAula($professor);
                    break;
                case '2':
                    $this->removerAula($professor);
                    break;
                case '0':
                    return;
                default:
                    echo self::COR_ERRO . "Opção inválida!\n" . self::COR_RESET;
                    sleep(1);
            }
        }
    }

    private function agendarAula(array &$professor): void
    {
        echo "\nDisciplinas do professor:\n";
        foreach ($professor['disciplinas'] as $disciplinaId) {
            echo " - [$disciplinaId] {$this->disciplinas[$disciplinaId]['nome']}\n";
        }

        echo "\nID da Disciplina: ";
        $disciplinaId = trim(fgets(STDIN));

        if (!in_array($disciplinaId, $professor['disciplinas'])) {
            echo self::COR_ERRO . "\nProfessor não está associado a esta disciplina.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "\nTurmas do professor:\n";
        foreach ($professor['turmas'] as $turmaId) {
            echo " - [$turmaId] {$this->turmas[$turmaId]['nome']}\n";
        }

        echo "\nID da Turma: ";
        $turmaId = trim(fgets(STDIN));

        if (!in_array($turmaId, $professor['turmas'])) {
            echo self::COR_ERRO . "\nProfessor não está alocado nesta turma.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "\nDia da semana: ";
        $diaSemana = trim(fgets(STDIN));

        echo "Hora (HH:MM): ";
        $hora = trim(fgets(STDIN));

        $id = uniqid();
        $professor['aulas'][$id] = [
            'id' => $id,
            'disciplina_id' => $disciplinaId,
            'turma_id' => $turmaId,
            'dia_semana' => $diaSemana,
            'hora' => $hora
        ];

        echo self::COR_SUCESSO . "\nAula agendada com sucesso!\n" . self::COR_RESET;
        sleep(2);
    }

    private function removerAula(array &$professor): void
    {
        if (empty($professor['aulas'])) {
            echo self::COR_AVISO . "Nenhuma aula agendada para este professor.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "\nID da Aula para remover: ";
        $aulaId = trim(fgets(STDIN));

        if (!isset($professor['aulas'][$aulaId])) {
            echo self::COR_ERRO . "\nAula não encontrada.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        unset($professor['aulas'][$aulaId]);
        echo self::COR_SUCESSO . "\nAula removida com sucesso!\n" . self::COR_RESET;
        sleep(2);
    }

    private function visualizarProfessores(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== VISUALIZAR PROFESSORES ===\n\n" . self::COR_RESET;

        if (empty($this->professores)) {
            echo self::COR_AVISO . "Nenhum professor cadastrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        foreach ($this->professores as $professor) {
            echo self::COR_DESTAQUE . "{$professor['nome']} ({$professor['especializacao']})\n" . self::COR_RESET;
            echo "Tipo: {$professor['tipo']}\n";
            echo "E-mail: {$professor['email']}\n";
            
            if (!empty($professor['disciplinas'])) {
                echo "\nDisciplinas:\n";
                foreach ($professor['disciplinas'] as $disciplinaId) {
                    echo " - {$this->disciplinas[$disciplinaId]['nome']}\n";
                }
            }
            
            if (!empty($professor['turmas'])) {
                echo "\nTurmas:\n";
                foreach ($professor['turmas'] as $turmaId) {
                    echo " - {$this->turmas[$turmaId]['nome']}\n";
                }
            }
            
            if (!empty($professor['aulas'])) {
                echo "\nCronograma:\n";
                foreach ($professor['aulas'] as $aula) {
                    $disciplina = $this->disciplinas[$aula['disciplina_id']]['nome'] ?? 'Disciplina não encontrada';
                    $turma = $this->turmas[$aula['turma_id']]['nome'] ?? 'Turma não encontrada';
                    echo " - {$disciplina} com {$turma} - {$aula['dia_semana']} às {$aula['hora']}\n";
                }
            }
            
            echo "\n" . str_repeat("-", 50) . "\n\n";
        }

        echo self::COR_SUCESSO . "Pressione Enter para continuar..." . self::COR_RESET;
        fgets(STDIN);
    }
}
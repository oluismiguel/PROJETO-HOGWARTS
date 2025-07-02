<?php
namespace Src\Model;

use Exception;

class SelecaoDeCasas
{
    private const COR_TITULO = "\033[1;36m";
    private const COR_SUCESSO = "\033[1;32m";
    private const COR_ERRO = "\033[1;31m";
    private const COR_AVISO = "\033[1;33m";
    private const COR_RESET = "\033[0m";
    private const COR_DESTAQUE = "\033[1;35m";

    private const CASAS = [
        'G' => ['nome' => 'Grifinória', 'cor' => "\033[38;5;160m", 'traco' => 'coragem'],
        'S' => ['nome' => 'Sonserina', 'cor' => "\033[38;5;28m", 'traco' => 'ambição'],
        'C' => ['nome' => 'Corvinal', 'cor' => "\033[38;5;27m", 'traco' => 'inteligência'],
        'L' => ['nome' => 'Lufa-Lufa', 'cor' => "\033[38;5;220m", 'traco' => 'lealdade']
    ];

    private array $alunos = [];
    private string $arquivoAlunos;

    public function __construct()
    {
        $this->arquivoAlunos = __DIR__ . '/../../data/alunos.json';
        $this->carregarDados();
    }

    public function executar(): void
    {
        while (true) {
            $this->limparTela();
            $this->exibirMenu();
            $opcao = $this->lerOpcao();

            switch ($opcao) {
                case '1':
                    $this->registrarCaracteristicas();
                    break;
                case '2':
                    $this->realizarSelecao();
                    break;
                case '3':
                    $this->consultarDistribuicao();
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
        echo "         MÓDULO 2 - SELEÇÃO DE CASAS\n";
        echo "=============================================\n\n" . self::COR_RESET;

        echo self::COR_DESTAQUE . "MENU:\n" . self::COR_RESET;
        echo "  [1] Registrar características do aluno\n";
        echo "  [2] Realizar seleção de casa\n";
        echo "  [3] Consultar distribuição por casa\n";
        echo "  [0] Voltar ao menu principal\n\n";
    }

    private function carregarDados(): void
    {
        if (file_exists($this->arquivoAlunos)) {
            $dados = file_get_contents($this->arquivoAlunos);
            $this->alunos = json_decode($dados, true) ?? [];
        } else {
            if (!file_exists(dirname($this->arquivoAlunos))) {
                mkdir(dirname($this->arquivoAlunos), 0777, true);
            }
            $this->alunos = [];
            $this->salvarDados();
        }
    }

    private function salvarDados(): void
    {
        file_put_contents($this->arquivoAlunos, json_encode($this->alunos, JSON_PRETTY_PRINT));
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

    private function listarAlunosSemCaracteristicas(): array
    {
        return array_filter($this->alunos, function($aluno) {
            return !isset($aluno['caracteristicas']);
        });
    }

    private function listarAlunosSemCasa(): array
    {
        return array_filter($this->alunos, function($aluno) {
            return isset($aluno['caracteristicas']) && !isset($aluno['casa']);
        });
    }

    private function registrarCaracteristicas(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== REGISTRAR CARACTERÍSTICAS ===\n\n" . self::COR_RESET;

        $alunosSemDados = $this->listarAlunosSemCaracteristicas();

        if (empty($alunosSemDados)) {
            echo self::COR_SUCESSO . "Todos os alunos já têm características registradas.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "Alunos sem características registradas:\n";
        foreach ($alunosSemDados as $aluno) {
            echo " - [{$aluno['id']}] {$aluno['nome']}\n";
        }

        echo "\nDigite o ID do aluno: ";
        $id = trim(fgets(STDIN));

        if (!isset($this->alunos[$id])) {
            echo self::COR_ERRO . "\nAluno não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $aluno = $this->alunos[$id];

        echo "\nRegistrando características para {$aluno['nome']}:\n";

        $caracteristicas = [];
        foreach (self::CASAS as $casa) {
            echo "{$casa['nome']} ({$casa['traco']}): ";
            $valor = (int)trim(fgets(STDIN));
            $caracteristicas[strtolower($casa['traco'])] = $valor;
        }

        $this->alunos[$id]['caracteristicas'] = $caracteristicas;

        echo self::COR_SUCESSO . "\nCaracterísticas registradas com sucesso!\n" . self::COR_RESET;
        sleep(2);
    }

    private function realizarSelecao(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== CERIMÔNIA DE SELEÇÃO ===\n\n" . self::COR_RESET;

        $alunosParaSelecionar = $this->listarAlunosSemCasa();

        if (empty($alunosParaSelecionar)) {
            echo self::COR_SUCESSO . "Todos os alunos já foram selecionados para suas casas.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "Alunos aguardando seleção:\n";
        foreach ($alunosParaSelecionar as $aluno) {
            echo " - [{$aluno['id']}] {$aluno['nome']}\n";
        }

        echo "\nDigite o ID do aluno: ";
        $id = trim(fgets(STDIN));

        if (!isset($this->alunos[$id]) || isset($this->alunos[$id]['casa'])) {
            echo self::COR_ERRO . "\nAluno não encontrado ou já selecionado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $aluno = $this->alunos[$id];
        $this->processarSelecao($aluno, $id);
    }

    private function processarSelecao(array $aluno, string $id): void
    {
        echo "\nAnalisando {$aluno['nome']}...\n";
        sleep(1);

        $sugestao = $this->calcularCasaSugerida($aluno['caracteristicas']);
        $casaSugerida = self::CASAS[$sugestao];

        echo "\nO Chapéu Seletor está pensando...\n";
        sleep(2);

        echo "\nSugestão do sistema: ";
        echo $casaSugerida['cor'] . $casaSugerida['nome'] . self::COR_RESET . "\n";

        echo "\nO Chapéu Seletor deve:\n";
        echo " [1] Aceitar sugestão\n";
        echo " [2] Escolher outra casa\n";
        echo "Opção: ";
        $opcao = trim(fgets(STDIN));

        if ($opcao === '1') {
            $casaEscolhida = $sugestao;
        } else {
            echo "\nEscolha a casa:\n";
            foreach (self::CASAS as $sigla => $casa) {
                echo " [{$sigla}] {$casa['cor']}{$casa['nome']}" . self::COR_RESET . "\n";
            }
            echo "Opção: ";
            $casaEscolhida = strtoupper(trim(fgets(STDIN)));
        }

        if (!isset(self::CASAS[$casaEscolhida])) {
            echo self::COR_ERRO . "\nCasa inválida!\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $this->alunos[$id]['casa'] = $casaEscolhida;
        $this->alunos[$id]['data_selecao'] = date('d/m/Y H:i:s');

        $casa = self::CASAS[$casaEscolhida];
        echo "\n" . $casa['cor'] . "PARABÉNS! {$aluno['nome']} foi selecionado(a) para {$casa['nome']}!" . self::COR_RESET . "\n";
        sleep(3);
    }

    private function calcularCasaSugerida(array $caracteristicas): string
    {
        $pontuacao = [];
        foreach (self::CASAS as $sigla => $casa) {
            $traco = strtolower($casa['traco']);
            $pontuacao[$sigla] = $caracteristicas[$traco] ?? 0;
        }

        arsort($pontuacao);
        return key($pontuacao);
    }

    private function consultarDistribuicao(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== DISTRIBUIÇÃO POR CASA ===\n\n" . self::COR_RESET;

        $distribuicao = [];
        $total = 0;

        foreach (self::CASAS as $sigla => $casa) {
            $distribuicao[$sigla] = [
                'nome' => $casa['nome'],
                'cor' => $casa['cor'],
                'quantidade' => 0,
                'alunos' => []
            ];
        }

        foreach ($this->alunos as $aluno) {
            if (isset($aluno['casa'])) {
                $distribuicao[$aluno['casa']]['quantidade']++;
                $distribuicao[$aluno['casa']]['alunos'][] = $aluno['nome'];
                $total++;
            }
        }

        echo "Distribuição atual:\n\n";
        foreach ($distribuicao as $casa) {
            echo $casa['cor'] . str_pad("{$casa['nome']}: ", 15) . self::COR_RESET;
            echo "{$casa['quantidade']} alunos (" . round($casa['quantidade'] / max($total, 1) * 100) . "%)\n";
        }

        echo "\n" . self::COR_DESTAQUE . "Detalhes por casa:" . self::COR_RESET . "\n";
        foreach ($distribuicao as $sigla => $casa) {
            if ($casa['quantidade'] > 0) {
                echo "\n" . $casa['cor'] . "=== {$casa['nome']} ===" . self::COR_RESET . "\n";
                echo implode("\n", $casa['alunos']) . "\n";
            }
        }

        echo "\n" . self::COR_DESTAQUE . "Pressione Enter para continuar..." . self::COR_RESET;
        fgets(STDIN);
    }
}
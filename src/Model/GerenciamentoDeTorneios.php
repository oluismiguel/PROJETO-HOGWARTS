<?php
namespace Src\Model;

use Exception;

class GerenciamentoDeTorneios
{
    private const COR_TITULO = "\033[1;36m";
    private const COR_SUCESSO = "\033[1;32m";
    private const COR_ERRO = "\033[1;31m";
    private const COR_AVISO = "\033[1;33m";
    private const COR_RESET = "\033[0m";
    private const COR_DESTAQUE = "\033[1;35m";

    private array $torneios = [];
    private array $alunos = [];
    private string $arquivoTorneios;
    private string $arquivoAlunos;

    public function __construct()
    {
        $this->arquivoTorneios = __DIR__ . '/../../data/torneios.json';
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
                    $this->criarTorneio();
                    break;
                case '2':
                    $this->gerenciarDesafios();
                    break;
                case '3':
                    $this->gerenciarInscricoes();
                    break;
                case '4':
                    $this->registrarResultados();
                    break;
                case '5':
                    $this->visualizarRankings();
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
        echo "      MÓDULO 3 - TORNEIOS E COMPETIÇÕES\n";
        echo "=============================================\n\n" . self::COR_RESET;

        echo self::COR_DESTAQUE . "MENU:\n" . self::COR_RESET;
        echo "  [1] Criar/Editar Torneio\n";
        echo "  [2] Gerenciar Desafios\n";
        echo "  [3] Gerenciar Inscrições\n";
        echo "  [4] Registrar Resultados\n";
        echo "  [5] Visualizar Rankings\n";
        echo "  [0] Voltar ao menu principal\n\n";
    }

    private function carregarDados(): void
    {
        // Carrega dados dos torneios
        if (file_exists($this->arquivoTorneios)) {
            $dados = file_get_contents($this->arquivoTorneios);
            $this->torneios = json_decode($dados, true) ?? [];
        } else {
            if (!file_exists(dirname($this->arquivoTorneios))) {
                mkdir(dirname($this->arquivoTorneios), 0777, true);
            }
            $this->torneios = [];
        }

        // Carrega dados dos alunos
        if (file_exists($this->arquivoAlunos)) {
            $dados = file_get_contents($this->arquivoAlunos);
            $this->alunos = json_decode($dados, true) ?? [];
        }
    }

    private function salvarDados(): void
    {
        file_put_contents($this->arquivoTorneios, json_encode($this->torneios, JSON_PRETTY_PRINT));
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

    private function criarTorneio(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== CRIAR/EDITAR TORNEIO ===\n\n" . self::COR_RESET;

        // Lista torneios existentes
        if (!empty($this->torneios)) {
            echo "Torneios existentes:\n";
            foreach ($this->torneios as $id => $torneio) {
                echo " - [$id] {$torneio['nome']} ({$torneio['status']})\n";
            }
            echo "\n";
        }

        echo "Digite ID do torneio (ou novo para criar): ";
        $id = trim(fgets(STDIN));

        $torneio = $this->torneios[$id] ?? [
            'id' => $id,
            'nome' => '',
            'descricao' => '',
            'tipo' => 'Copa das Casas',
            'data_inicio' => '',
            'data_fim' => '',
            'local' => 'Salão Principal',
            'status' => 'planejado',
            'desafios' => [],
            'inscricoes' => [],
            'casas_participantes' => ['G', 'S', 'C', 'L']
        ];

        echo "\nNome do Torneio: ";
        $torneio['nome'] = trim(fgets(STDIN));

        echo "Descrição: ";
        $torneio['descricao'] = trim(fgets(STDIN));

        echo "Tipo (Copa das Casas/Torneio Tribruxo/Outro): ";
        $torneio['tipo'] = trim(fgets(STDIN));

        echo "Data de Início (DD/MM/AAAA): ";
        $torneio['data_inicio'] = trim(fgets(STDIN));

        echo "Data de Término (DD/MM/AAAA): ";
        $torneio['data_fim'] = trim(fgets(STDIN));

        echo "Local: ";
        $torneio['local'] = trim(fgets(STDIN));

        echo "Status (planejado/andamento/concluido/cancelado): ";
        $torneio['status'] = trim(fgets(STDIN));

        $this->torneios[$id] = $torneio;

        echo self::COR_SUCESSO . "\nTorneio salvo com sucesso!\n" . self::COR_RESET;
        sleep(2);
    }

    private function gerenciarDesafios(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== GERENCIAR DESAFIOS ===\n\n" . self::COR_RESET;

        if (empty($this->torneios)) {
            echo self::COR_AVISO . "Nenhum torneio cadastrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "Selecione o torneio:\n";
        foreach ($this->torneios as $id => $torneio) {
            echo " - [$id] {$torneio['nome']}\n";
        }

        echo "\nID do Torneio: ";
        $torneioId = trim(fgets(STDIN));

        if (!isset($this->torneios[$torneioId])) {
            echo self::COR_ERRO . "\nTorneio não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $torneio = &$this->torneios[$torneioId];

        while (true) {
            $this->limparTela();
            echo self::COR_TITULO . "=== DESAFIOS DO TORNEIO {$torneio['nome']} ===\n\n" . self::COR_RESET;

            if (!empty($torneio['desafios'])) {
                echo "Desafios cadastrados:\n";
                foreach ($torneio['desafios'] as $id => $desafio) {
                    echo " - [$id] {$desafio['nome']} ({$desafio['data']}) - {$desafio['pontuacao_maxima']} pts\n";
                }
                echo "\n";
            }

            echo "Opções:\n";
            echo " [1] Adicionar Desafio\n";
            echo " [2] Editar Desafio\n";
            echo " [3] Remover Desafio\n";
            echo " [0] Voltar\n";
            echo "Opção: ";
            $opcao = trim(fgets(STDIN));

            switch ($opcao) {
                case '1':
                    $this->adicionarDesafio($torneio);
                    break;
                case '2':
                    $this->editarDesafio($torneio);
                    break;
                case '3':
                    $this->removerDesafio($torneio);
                    break;
                case '0':
                    return;
                default:
                    echo self::COR_ERRO . "Opção inválida!\n" . self::COR_RESET;
                    sleep(1);
            }
        }
    }

    private function adicionarDesafio(array &$torneio): void
    {
        echo "\nID do Desafio: ";
        $id = trim(fgets(STDIN));

        $desafio = [
            'id' => $id,
            'nome' => '',
            'descricao' => '',
            'tipo' => 'prova_magica',
            'data' => '',
            'hora' => '14:00',
            'pontuacao_maxima' => 100,
            'critérios' => []
        ];

        echo "Nome do Desafio: ";
        $desafio['nome'] = trim(fgets(STDIN));

        echo "Descrição: ";
        $desafio['descricao'] = trim(fgets(STDIN));

        echo "Tipo (prova_magica/duelo/enigma/tarefa): ";
        $desafio['tipo'] = trim(fgets(STDIN));

        echo "Data (DD/MM/AAAA): ";
        $desafio['data'] = trim(fgets(STDIN));

        echo "Pontuação Máxima: ";
        $desafio['pontuacao_maxima'] = (int)trim(fgets(STDIN));

        $torneio['desafios'][$id] = $desafio;

        echo self::COR_SUCESSO . "\nDesafio adicionado com sucesso!\n" . self::COR_RESET;
        sleep(2);
    }

    private function editarDesafio(array &$torneio): void
    {
        if (empty($torneio['desafios'])) {
            echo self::COR_AVISO . "Nenhum desafio cadastrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "\nID do Desafio para editar: ";
        $id = trim(fgets(STDIN));

        if (!isset($torneio['desafios'][$id])) {
            echo self::COR_ERRO . "\nDesafio não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $desafio = &$torneio['desafios'][$id];

        echo "Nome [{$desafio['nome']}]: ";
        $input = trim(fgets(STDIN));
        if (!empty($input)) $desafio['nome'] = $input;

        echo "Descrição [{$desafio['descricao']}]: ";
        $input = trim(fgets(STDIN));
        if (!empty($input)) $desafio['descricao'] = $input;

        echo "Data [{$desafio['data']}]: ";
        $input = trim(fgets(STDIN));
        if (!empty($input)) $desafio['data'] = $input;

        echo "Pontuação Máxima [{$desafio['pontuacao_maxima']}]: ";
        $input = trim(fgets(STDIN));
        if (!empty($input)) $desafio['pontuacao_maxima'] = (int)$input;

        echo self::COR_SUCESSO . "\nDesafio atualizado com sucesso!\n" . self::COR_RESET;
        sleep(2);
    }

    private function removerDesafio(array &$torneio): void
    {
        if (empty($torneio['desafios'])) {
            echo self::COR_AVISO . "Nenhum desafio cadastrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "\nID do Desafio para remover: ";
        $id = trim(fgets(STDIN));

        if (!isset($torneio['desafios'][$id])) {
            echo self::COR_ERRO . "\nDesafio não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        unset($torneio['desafios'][$id]);
        echo self::COR_SUCESSO . "\nDesafio removido com sucesso!\n" . self::COR_RESET;
        sleep(2);
    }

    private function gerenciarInscricoes(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== GERENCIAR INSCRIÇÕES ===\n\n" . self::COR_RESET;

        if (empty($this->torneios)) {
            echo self::COR_AVISO . "Nenhum torneio cadastrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "Selecione o torneio:\n";
        foreach ($this->torneios as $id => $torneio) {
            echo " - [$id] {$torneio['nome']} ({$torneio['status']})\n";
        }

        echo "\nID do Torneio: ";
        $torneioId = trim(fgets(STDIN));

        if (!isset($this->torneios[$torneioId])) {
            echo self::COR_ERRO . "\nTorneio não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $torneio = &$this->torneios[$torneioId];

        while (true) {
            $this->limparTela();
            echo self::COR_TITULO . "=== INSCRIÇÕES - {$torneio['nome']} ===\n\n" . self::COR_RESET;

            if (!empty($torneio['inscricoes'])) {
                echo "Inscrições:\n";
                foreach ($torneio['inscricoes'] as $alunoId => $status) {
                    $aluno = $this->alunos[$alunoId] ?? ['nome' => 'Aluno não encontrado'];
                    echo " - [$alunoId] {$aluno['nome']} ($status)\n";
                }
                echo "\n";
            }

            echo "Opções:\n";
            echo " [1] Adicionar Inscrição\n";
            echo " [2] Aprovar/Reprovar Inscrição\n";
            echo " [3] Remover Inscrição\n";
            echo " [0] Voltar\n";
            echo "Opção: ";
            $opcao = trim(fgets(STDIN));

            switch ($opcao) {
                case '1':
                    $this->adicionarInscricao($torneio);
                    break;
                case '2':
                    $this->alterarStatusInscricao($torneio);
                    break;
                case '3':
                    $this->removerInscricao($torneio);
                    break;
                case '0':
                    return;
                default:
                    echo self::COR_ERRO . "Opção inválida!\n" . self::COR_RESET;
                    sleep(1);
            }
        }
    }

    private function adicionarInscricao(array &$torneio): void
{
    echo "\nAlunos disponíveis:\n";
    foreach ($this->alunos as $id => $aluno) {
        if (isset($aluno['casa']) && in_array($aluno['casa'], $torneio['casas_participantes'])) {
            $jaInscrito = isset($torneio['inscricoes'][$id]);
            echo " - [$id] {$aluno['nome']} ({$aluno['casa']})" . ($jaInscrito ? " [JÁ INSCRITO]" : "") . "\n";
        }
    }

    echo "\nID do Aluno: ";
    $alunoId = trim(fgets(STDIN));

    if (!isset($this->alunos[$alunoId])) {
        echo self::COR_ERRO . "\nAluno não encontrado.\n" . self::COR_RESET;
        sleep(2);
        return;
    }

    $aluno = $this->alunos[$alunoId];
    
    // Verifica se aluno já está inscrito
    if (isset($torneio['inscricoes'][$alunoId])) {
        echo self::COR_AVISO . "\nAluno já está inscrito neste torneio.\n" . self::COR_RESET;
        sleep(2);
        return;
    }

    // Verifica se a casa do aluno está participando
    if (!in_array($aluno['casa'], $torneio['casas_participantes'])) {
        echo self::COR_ERRO . "\nA casa do aluno não está participando deste torneio.\n" . self::COR_RESET;
        sleep(2);
        return;
    }

    $torneio['inscricoes'][$alunoId] = 'pendente';
    echo self::COR_SUCESSO . "\nInscrição adicionada com sucesso! Status: pendente\n" . self::COR_RESET;
    sleep(2);
}

    private function alterarStatusInscricao(array &$torneio): void
    {
        if (empty($torneio['inscricoes'])) {
            echo self::COR_AVISO . "Nenhuma inscrição cadastrada.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "\nID do Aluno para alterar status: ";
        $alunoId = trim(fgets(STDIN));

        if (!isset($torneio['inscricoes'][$alunoId])) {
            echo self::COR_ERRO . "\nInscrição não encontrada.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $statusAtual = $torneio['inscricoes'][$alunoId];
        echo "Status atual: $statusAtual\n";
        echo "Novo status (pendente/aprovada/reprovada): ";
        $novoStatus = trim(fgets(STDIN));

        if (!in_array($novoStatus, ['pendente', 'aprovada', 'reprovada'])) {
            echo self::COR_ERRO . "\nStatus inválido.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $torneio['inscricoes'][$alunoId] = $novoStatus;
        echo self::COR_SUCESSO . "\nStatus da inscrição atualizado com sucesso!\n" . self::COR_RESET;
        sleep(2);
    }

    private function removerInscricao(array &$torneio): void
    {
        if (empty($torneio['inscricoes'])) {
            echo self::COR_AVISO . "Nenhuma inscrição cadastrada.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "\nID do Aluno para remover inscrição: ";
        $alunoId = trim(fgets(STDIN));

        if (!isset($torneio['inscricoes'][$alunoId])) {
            echo self::COR_ERRO . "\nInscrição não encontrada.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        unset($torneio['inscricoes'][$alunoId]);
        echo self::COR_SUCESSO . "\nInscrição removida com sucesso!\n" . self::COR_RESET;
        sleep(2);
    }

    private function registrarResultados(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== REGISTRAR RESULTADOS ===\n\n" . self::COR_RESET;

        if (empty($this->torneios)) {
            echo self::COR_AVISO . "Nenhum torneio cadastrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "Selecione o torneio:\n";
        foreach ($this->torneios as $id => $torneio) {
            echo " - [$id] {$torneio['nome']}\n";
        }

        echo "\nID do Torneio: ";
        $torneioId = trim(fgets(STDIN));

        if (!isset($this->torneios[$torneioId])) {
            echo self::COR_ERRO . "\nTorneio não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $torneio = &$this->torneios[$torneioId];

        if (empty($torneio['desafios'])) {
            echo self::COR_AVISO . "Nenhum desafio cadastrado neste torneio.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "\nSelecione o desafio:\n";
        foreach ($torneio['desafios'] as $id => $desafio) {
            echo " - [$id] {$desafio['nome']} ({$desafio['data']})\n";
        }

        echo "\nID do Desafio: ";
        $desafioId = trim(fgets(STDIN));

        if (!isset($torneio['desafios'][$desafioId])) {
            echo self::COR_ERRO . "\nDesafio não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $desafio = &$torneio['desafios'][$desafioId];

        if (empty($torneio['inscricoes'])) {
            echo self::COR_AVISO . "Nenhum aluno inscrito neste torneio.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "\nRegistrar pontuação para cada aluno:\n";
        foreach ($torneio['inscricoes'] as $alunoId => $status) {
            if ($status !== 'aprovada') continue;

            $aluno = $this->alunos[$alunoId] ?? ['nome' => 'Aluno não encontrado'];
            echo "\nAluno: {$aluno['nome']} ({$aluno['casa']})\n";
            echo "Pontuação (0-{$desafio['pontuacao_maxima']}): ";
            $pontuacao = (int)trim(fgets(STDIN));

            if ($pontuacao < 0 || $pontuacao > $desafio['pontuacao_maxima']) {
                echo self::COR_AVISO . "Pontuação inválida, usando 0 como padrão.\n" . self::COR_RESET;
                $pontuacao = 0;
            }

            if (!isset($desafio['resultados'])) {
                $desafio['resultados'] = [];
            }

            $desafio['resultados'][$alunoId] = $pontuacao;
        }

        echo self::COR_SUCESSO . "\nResultados registrados com sucesso!\n" . self::COR_RESET;
        sleep(2);
    }

    private function visualizarRankings(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== VISUALIZAR RANKINGS ===\n\n" . self::COR_RESET;

        if (empty($this->torneios)) {
            echo self::COR_AVISO . "Nenhum torneio cadastrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "Selecione o torneio:\n";
        foreach ($this->torneios as $id => $torneio) {
            echo " - [$id] {$torneio['nome']}\n";
        }

        echo "\nID do Torneio: ";
        $torneioId = trim(fgets(STDIN));

        if (!isset($this->torneios[$torneioId])) {
            echo self::COR_ERRO . "\nTorneio não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $torneio = $this->torneios[$torneioId];

        // Calcular pontuações totais por casa
        $pontuacaoCasas = ['G' => 0, 'S' => 0, 'C' => 0, 'L' => 0];
        $pontuacaoAlunos = [];

        foreach ($torneio['desafios'] as $desafio) {
            if (!isset($desafio['resultados'])) continue;

            foreach ($desafio['resultados'] as $alunoId => $pontuacao) {
                if (!isset($this->alunos[$alunoId])) continue;

                $aluno = $this->alunos[$alunoId];
                $casa = $aluno['casa'];

                // Atualiza pontuação da casa
                if (isset($pontuacaoCasas[$casa])) {
                    $pontuacaoCasas[$casa] += $pontuacao;
                }

                // Atualiza pontuação do aluno
                if (!isset($pontuacaoAlunos[$alunoId])) {
                    $pontuacaoAlunos[$alunoId] = [
                        'nome' => $aluno['nome'],
                        'casa' => $casa,
                        'pontos' => 0
                    ];
                }
                $pontuacaoAlunos[$alunoId]['pontos'] += $pontuacao;
            }
        }

        // Ordenar rankings
        arsort($pontuacaoCasas);
        usort($pontuacaoAlunos, function($a, $b) {
            return $b['pontos'] - $a['pontos'];
        });

        // Exibir ranking de casas
        echo "\n" . self::COR_DESTAQUE . "RANKING DE CASAS:\n" . self::COR_RESET;
        foreach ($pontuacaoCasas as $casa => $pontos) {
            echo " - Casa " . $this->getNomeCasa($casa) . ": $pontos pontos\n";
        }

        // Exibir ranking individual
        echo "\n" . self::COR_DESTAQUE . "RANKING INDIVIDUAL:\n" . self::COR_RESET;
        foreach ($pontuacaoAlunos as $aluno) {
            echo " - {$aluno['nome']} ({$this->getNomeCasa($aluno['casa'])}): {$aluno['pontos']} pontos\n";
        }

        echo "\n" . self::COR_SUCESSO . "Pressione Enter para continuar..." . self::COR_RESET;
        fgets(STDIN);
    }

    private function getNomeCasa(string $sigla): string
    {
        $casas = [
            'G' => 'Grifinória',
            'S' => 'Sonserina',
            'C' => 'Corvinal',
            'L' => 'Lufa-Lufa'
        ];
        return $casas[$sigla] ?? $sigla;
    }
}
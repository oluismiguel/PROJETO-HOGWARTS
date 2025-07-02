<?php
namespace Src\Model;

use Exception;

class ControleAcademicoDisciplinar
{
    private const COR_TITULO = "\033[1;36m";
    private const COR_SUCESSO = "\033[1;32m";
    private const COR_ERRO = "\033[1;31m";
    private const COR_AVISO = "\033[1;33m";
    private const COR_RESET = "\033[0m";
    private const COR_DESTAQUE = "\033[1;35m";

    private array $alunos = [];
    private array $professores = [];
    private array $disciplinas = [];
    private array $registrosAcademicos = [];
    private array $registrosDisciplinares = [];
    private array $pontuacaoCasas = ['G' => 0, 'S' => 0, 'C' => 0, 'L' => 0];
    private string $arquivoDados;

    public function __construct()
    {
        $this->arquivoDados = __DIR__ . '/../../data/controle_academico.json';
        $this->carregarDados();
        $this->carregarAlunosDoModulo1();
    }

    private function carregarAlunosDoModulo1(): void
    {
        $arquivoAlunos = __DIR__ . '/../../data/alunos.json';
        if (file_exists($arquivoAlunos)) {
            $this->alunos = json_decode(file_get_contents($arquivoAlunos), true) ?? [];
            
            // Inicializa a casa para alunos que ainda não têm casa definida
            foreach ($this->alunos as &$aluno) {
                if (!isset($aluno['casa'])) {
                    $aluno['casa'] = 'N/D';
                }
            }
        }
    }

    public function executar(): void
    {
        while (true) {
            $this->limparTela();
            $this->exibirMenu();
            $opcao = $this->lerOpcao();

            switch ($opcao) {
                case '1':
                    $this->registrarNota();
                    break;
                case '2':
                    $this->registrarOcorrencia();
                    break;
                case '3':
                    $this->gerenciarPontuacao();
                    break;
                case '4':
                    $this->visualizarBoletim();
                    break;
                case '5':
                    $this->visualizarPontuacaoCasas();
                    break;
                case '6':
                    $this->gerarRelatorios();
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
        echo "      MÓDULO 4 - CONTROLE ACADÊMICO E DISCIPLINAR\n";
        echo "=============================================\n\n" . self::COR_RESET;

        echo self::COR_DESTAQUE . "MENU:\n" . self::COR_RESET;
        echo "  [1] Registrar Nota Acadêmica\n";
        echo "  [2] Registrar Ocorrência Disciplinar\n";
        echo "  [3] Gerenciar Pontuação das Casas\n";
        echo "  [4] Visualizar Boletim do Aluno\n";
        echo "  [5] Visualizar Pontuação das Casas\n";
        echo "  [6] Gerar Relatórios\n";
        echo "  [0] Voltar ao menu principal\n\n";
    }

    private function carregarDados(): void
    {
        if (file_exists($this->arquivoDados)) {
            $dados = file_get_contents($this->arquivoDados);
            $dadosArray = json_decode($dados, true) ?? [];
            
            $this->registrosAcademicos = $dadosArray['registrosAcademicos'] ?? [];
            $this->registrosDisciplinares = $dadosArray['registrosDisciplinares'] ?? [];
            $this->pontuacaoCasas = $dadosArray['pontuacaoCasas'] ?? ['G' => 0, 'S' => 0, 'C' => 0, 'L' => 0];
        } else {
            if (!file_exists(dirname($this->arquivoDados))) {
                mkdir(dirname($this->arquivoDados), 0777, true);
            }
            $this->salvarDados();
        }

        // Carregar dados complementares
        $arquivoProfessores = __DIR__ . '/../../data/professores.json';
        if (file_exists($arquivoProfessores)) {
            $this->professores = json_decode(file_get_contents($arquivoProfessores), true) ?? [];
        }

        $arquivoDisciplinas = __DIR__ . '/../../data/disciplinas.json';
        if (file_exists($arquivoDisciplinas)) {
            $this->disciplinas = json_decode(file_get_contents($arquivoDisciplinas), true) ?? [];
        }
    }

    private function salvarDados(): void
    {
        $dados = [
            'registrosAcademicos' => $this->registrosAcademicos,
            'registrosDisciplinares' => $this->registrosDisciplinares,
            'pontuacaoCasas' => $this->pontuacaoCasas
        ];

        file_put_contents($this->arquivoDados, json_encode($dados, JSON_PRETTY_PRINT));
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

    private function registrarNota(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== REGISTRAR NOTA ACADÊMICA ===\n\n" . self::COR_RESET;

        if (empty($this->alunos)) {
            echo self::COR_AVISO . "Nenhum aluno cadastrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Listar alunos
        echo "Alunos disponíveis:\n";
        foreach ($this->alunos as $id => $aluno) {
            echo " - [$id] {$aluno['nome']} ({$this->getNomeCasa($aluno['casa'])})\n";
        }

        echo "\nID do Aluno: ";
        $alunoId = trim(fgets(STDIN));

        if (!isset($this->alunos[$alunoId])) {
            echo self::COR_ERRO . "\nAluno não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Listar disciplinas
        if (empty($this->disciplinas)) {
            echo self::COR_AVISO . "Nenhuma disciplina cadastrada.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

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

        echo "\nNota (0-10): ";
        $nota = (float)trim(fgets(STDIN));

        if ($nota < 0 || $nota > 10) {
            echo self::COR_AVISO . "Nota inválida, usando 0 como padrão.\n" . self::COR_RESET;
            $nota = 0;
        }

        echo "Observações: ";
        $observacoes = trim(fgets(STDIN));

        // Registrar nota
        $registro = [
            'id' => uniqid(),
            'aluno_id' => $alunoId,
            'disciplina_id' => $disciplinaId,
            'nota' => $nota,
            'data' => date('d/m/Y'),
            'observacoes' => $observacoes
        ];

        $this->registrosAcademicos[] = $registro;

        // Aplicar pontos de mérito se a nota for alta
        $casaAluno = $this->alunos[$alunoId]['casa'];
        if ($casaAluno !== 'N/D') {
            if ($nota >= 9) {
                $this->adicionarPontosCasa($casaAluno, 10, "Excelente desempenho em {$this->disciplinas[$disciplinaId]['nome']}");
            } elseif ($nota >= 7) {
                $this->adicionarPontosCasa($casaAluno, 5, "Bom desempenho em {$this->disciplinas[$disciplinaId]['nome']}");
            }
        }

        echo self::COR_SUCESSO . "\nNota registrada com sucesso!\n" . self::COR_RESET;
        sleep(2);
    }

    private function registrarOcorrencia(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== REGISTRAR OCORRÊNCIA DISCIPLINAR ===\n\n" . self::COR_RESET;

        if (empty($this->alunos)) {
            echo self::COR_AVISO . "Nenhum aluno cadastrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Listar alunos
        echo "Alunos disponíveis:\n";
        foreach ($this->alunos as $id => $aluno) {
            echo " - [$id] {$aluno['nome']} ({$this->getNomeCasa($aluno['casa'])})\n";
        }

        echo "\nID do Aluno: ";
        $alunoId = trim(fgets(STDIN));

        if (!isset($this->alunos[$alunoId])) {
            echo self::COR_ERRO . "\nAluno não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "\nTipo de ocorrência:\n";
        echo " [1] Comportamento exemplar\n";
        echo " [2] Infração leve\n";
        echo " [3] Infração moderada\n";
        echo " [4] Infração grave\n";
        echo "Opção: ";
        $tipo = trim(fgets(STDIN));

        $tiposOcorrencia = [
            '1' => ['gravidade' => 'positivo', 'pontos' => 10, 'descricao' => 'Comportamento exemplar'],
            '2' => ['gravidade' => 'leve', 'pontos' => -5, 'descricao' => 'Infração leve'],
            '3' => ['gravidade' => 'moderada', 'pontos' => -10, 'descricao' => 'Infração moderada'],
            '4' => ['gravidade' => 'grave', 'pontos' => -20, 'descricao' => 'Infração grave']
        ];

        if (!isset($tiposOcorrencia[$tipo])) {
            echo self::COR_ERRO . "\nTipo inválido.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "Descrição detalhada: ";
        $descricao = trim(fgets(STDIN));

        // Registrar ocorrência
        $ocorrencia = [
            'id' => uniqid(),
            'aluno_id' => $alunoId,
            'tipo' => $tiposOcorrencia[$tipo]['gravidade'],
            'descricao' => $descricao,
            'data' => date('d/m/Y H:i:s'),
            'pontos' => $tiposOcorrencia[$tipo]['pontos'],
            'tipo_descricao' => $tiposOcorrencia[$tipo]['descricao']
        ];

        $this->registrosDisciplinares[] = $ocorrencia;

        // Aplicar pontos à casa se o aluno tiver casa definida
        $casaAluno = $this->alunos[$alunoId]['casa'];
        if ($casaAluno !== 'N/D') {
            $this->adicionarPontosCasa(
                $casaAluno,
                $tiposOcorrencia[$tipo]['pontos'],
                "Ocorrência disciplinar: {$tiposOcorrencia[$tipo]['descricao']}"
            );
        }

        echo self::COR_SUCESSO . "\nOcorrência registrada com sucesso!\n" . self::COR_RESET;
        sleep(2);
    }

    private function gerenciarPontuacao(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== GERENCIAR PONTUAÇÃO DAS CASAS ===\n\n" . self::COR_RESET;

        echo "Selecione a casa:\n";
        echo " [1] Grifinória (G)\n";
        echo " [2] Sonserina (S)\n";
        echo " [3] Corvinal (C)\n";
        echo " [4] Lufa-Lufa (L)\n";
        echo "Opção: ";
        $opcaoCasa = trim(fgets(STDIN));

        $casas = ['1' => 'G', '2' => 'S', '3' => 'C', '4' => 'L'];
        if (!isset($casas[$opcaoCasa])) {
            echo self::COR_ERRO . "\nOpção inválida.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $casa = $casas[$opcaoCasa];
        $pontosAtuais = $this->pontuacaoCasas[$casa];

        echo "\nPontuação atual da {$this->getNomeCasa($casa)}: {$pontosAtuais}\n";
        echo "Deseja adicionar ou remover pontos? (A/R/Cancelar): ";
        $acao = strtoupper(trim(fgets(STDIN)));

        if ($acao === 'A') {
            echo "Quantos pontos deseja adicionar: ";
            $pontos = (int)trim(fgets(STDIN));
            $this->adicionarPontosCasa($casa, $pontos, "Ajuste manual pelo professor");
            echo self::COR_SUCESSO . "\n{$pontos} pontos adicionados à {$this->getNomeCasa($casa)}!\n" . self::COR_RESET;
        } elseif ($acao === 'R') {
            echo "Quantos pontos deseja remover: ";
            $pontos = (int)trim(fgets(STDIN));
            $this->adicionarPontosCasa($casa, -$pontos, "Ajuste manual pelo professor");
            echo self::COR_SUCESSO . "\n{$pontos} pontos removidos da {$this->getNomeCasa($casa)}!\n" . self::COR_RESET;
        } else {
            echo self::COR_AVISO . "\nOperação cancelada.\n" . self::COR_RESET;
        }
        sleep(2);
    }

    private function adicionarPontosCasa(string $casa, int $pontos, string $motivo): void
    {
        if (!isset($this->pontuacaoCasas[$casa])) {
            $this->pontuacaoCasas[$casa] = 0;
        }

        $this->pontuacaoCasas[$casa] += $pontos;

        // Registrar histórico de alteração
        $alteracao = [
            'data' => date('d/m/Y H:i:s'),
            'casa' => $casa,
            'pontos' => $pontos,
            'motivo' => $motivo,
            'total_atual' => $this->pontuacaoCasas[$casa]
        ];

        if (!isset($this->registrosDisciplinares['historico_pontos'])) {
            $this->registrosDisciplinares['historico_pontos'] = [];
        }
        $this->registrosDisciplinares['historico_pontos'][] = $alteracao;
    }

private function visualizarBoletim(): void
{
    $this->limparTela();
    echo self::COR_TITULO . "=== VISUALIZAR BOLETIM ===\n\n" . self::COR_RESET;

    if (empty($this->alunos)) {
        echo self::COR_AVISO . "Nenhum aluno cadastrado.\n" . self::COR_RESET;
        sleep(2);
        return;
    }

    // Listar alunos
    echo "Alunos disponíveis:\n";
    foreach ($this->alunos as $id => $aluno) {
        echo " - [$id] {$aluno['nome']} (" . $this->getNomeCasa($aluno['casa']) . ")\n";
    }

    echo "\nID do Aluno: ";
    $alunoId = trim(fgets(STDIN));

    if (!isset($this->alunos[$alunoId])) {
        echo self::COR_ERRO . "\nAluno não encontrado.\n" . self::COR_RESET;
        sleep(2);
        return;
    }

    $aluno = $this->alunos[$alunoId];
        
        // Obter notas do aluno
        $notasAluno = array_filter($this->registrosAcademicos, function($registro) use ($alunoId) {
            return $registro['aluno_id'] === $alunoId;
        });

        // Obter ocorrências do aluno
        $ocorrenciasAluno = array_filter($this->registrosDisciplinares, function($registro) use ($alunoId) {
            return isset($registro['aluno_id']) && $registro['aluno_id'] === $alunoId;
        });

    $this->limparTela();
    echo self::COR_TITULO . "=== BOLETIM DE {$aluno['nome']} ===\n\n" . self::COR_RESET;
    echo "Casa: " . $this->getNomeCasa($aluno['casa']) . "\n"; // Linha 404 corrigida
    echo "Data de Nascimento: {$aluno['nascimento']}\n\n";

        // Exibir notas
        echo self::COR_DESTAQUE . "DESEMPENHO ACADÊMICO:\n" . self::COR_RESET;
        if (empty($notasAluno)) {
            echo "Nenhuma nota registrada.\n";
        } else {
            foreach ($notasAluno as $nota) {
                $disciplina = $this->disciplinas[$nota['disciplina_id']]['nome'] ?? 'Disciplina não encontrada';
                $corNota = $nota['nota'] >= 7 ? self::COR_SUCESSO : ($nota['nota'] >= 5 ? self::COR_AVISO : self::COR_ERRO);
                echo " - {$disciplina}: {$corNota}{$nota['nota']}" . self::COR_RESET . " ({$nota['data']})";
                if (!empty($nota['observacoes'])) {
                    echo " - Obs: {$nota['observacoes']}";
                }
                echo "\n";
            }
        }

        // Exibir ocorrências
        echo "\n" . self::COR_DESTAQUE . "REGISTROS DISCIPLINARES:\n" . self::COR_RESET;
        if (empty($ocorrenciasAluno)) {
            echo "Nenhuma ocorrência registrada.\n";
        } else {
            foreach ($ocorrenciasAluno as $ocorrencia) {
                $corOcorrencia = $ocorrencia['pontos'] > 0 ? self::COR_SUCESSO : self::COR_ERRO;
                echo " - [{$ocorrencia['data']}] {$ocorrencia['tipo_descricao']}: {$corOcorrencia}{$ocorrencia['pontos']} pontos" . self::COR_RESET;
                echo "\n   Descrição: {$ocorrencia['descricao']}\n";
            }
        }

        echo "\n" . self::COR_DESTAQUE . "PONTUAÇÃO TOTAL PARA A CASA: " . self::COR_RESET;
        if ($aluno['casa'] !== 'N/D') {
            echo $this->pontuacaoCasas[$aluno['casa']] . " pontos\n";
        } else {
            echo "Aluno não está em uma casa\n";
        }

        echo "\n" . self::COR_SUCESSO . "Pressione Enter para continuar..." . self::COR_RESET;
        fgets(STDIN);
    }

    private function visualizarPontuacaoCasas(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== PONTUAÇÃO DAS CASAS ===\n\n" . self::COR_RESET;

        echo "PONTUAÇÃO ATUAL:\n";
        foreach ($this->pontuacaoCasas as $sigla => $pontos) {
            echo " - {$this->getNomeCasa($sigla)}: {$pontos} pontos\n";
        }

        // Exibir histórico recente
        if (isset($this->registrosDisciplinares['historico_pontos']) && !empty($this->registrosDisciplinares['historico_pontos'])) {
            echo "\n" . self::COR_DESTAQUE . "ÚLTIMAS ALTERAÇÕES:\n" . self::COR_RESET;
            $historico = array_slice($this->registrosDisciplinares['historico_pontos'], -5); // Mostrar as 5 últimas
            foreach ($historico as $alteracao) {
                $cor = $alteracao['pontos'] > 0 ? self::COR_SUCESSO : self::COR_ERRO;
                echo " - [{$alteracao['data']}] {$this->getNomeCasa($alteracao['casa'])}: {$cor}{$alteracao['pontos']}" . self::COR_RESET;
                echo " pontos (Total: {$alteracao['total_atual']}) - Motivo: {$alteracao['motivo']}\n";
            }
        }

        echo "\n" . self::COR_SUCESSO . "Pressione Enter para continuar..." . self::COR_RESET;
        fgets(STDIN);
    }

    private function gerarRelatorios(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== GERAR RELATÓRIOS ===\n\n" . self::COR_RESET;

        echo "Selecione o tipo de relatório:\n";
        echo " [1] Desempenho por Disciplina\n";
        echo " [2] Comportamento por Casa\n";
        echo " [3] Ranking das Casas\n";
        echo " [0] Voltar\n";
        echo "Opção: ";
        $opcao = trim(fgets(STDIN));

        switch ($opcao) {
            case '1':
                $this->relatorioDesempenhoDisciplina();
                break;
            case '2':
                $this->relatorioComportamentoCasas();
                break;
            case '3':
                $this->relatorioRankingCasas();
                break;
            case '0':
                return;
            default:
                echo self::COR_ERRO . "Opção inválida!\n" . self::COR_RESET;
                sleep(1);
        }
    }

    private function relatorioDesempenhoDisciplina(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== RELATÓRIO DE DESEMPENHO POR DISCIPLINA ===\n\n" . self::COR_RESET;

        if (empty($this->disciplinas)) {
            echo self::COR_AVISO . "Nenhuma disciplina cadastrada.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "Selecione a disciplina:\n";
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

        $notasDisciplina = array_filter($this->registrosAcademicos, function($registro) use ($disciplinaId) {
            return $registro['disciplina_id'] === $disciplinaId;
        });

        if (empty($notasDisciplina)) {
            echo self::COR_AVISO . "\nNenhuma nota registrada para esta disciplina.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Calcular média
        $soma = 0;
        foreach ($notasDisciplina as $nota) {
            $soma += $nota['nota'];
        }
        $media = $soma / count($notasDisciplina);

        // Agrupar por casa
        $porCasa = [];
        foreach ($notasDisciplina as $nota) {
            $alunoId = $nota['aluno_id'];
            $casa = $this->alunos[$alunoId]['casa'] ?? 'N/D';
            if (!isset($porCasa[$casa])) {
                $porCasa[$casa] = [];
            }
            $porCasa[$casa][] = $nota['nota'];
        }

        // Exibir relatório
        echo "\nDisciplina: {$this->disciplinas[$disciplinaId]['nome']}\n";
        echo "Média geral: " . number_format($media, 2) . "\n";
        echo "Total de avaliações: " . count($notasDisciplina) . "\n\n";

        echo "Desempenho por casa:\n";
        foreach ($porCasa as $casa => $notas) {
            $mediaCasa = array_sum($notas) / count($notas);
            echo " - {$this->getNomeCasa($casa)}: " . number_format($mediaCasa, 2) . " (" . count($notas) . " alunos)\n";
        }

        echo "\n" . self::COR_SUCESSO . "Pressione Enter para continuar..." . self::COR_RESET;
        fgets(STDIN);
    }

    private function relatorioComportamentoCasas(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== RELATÓRIO DE COMPORTAMENTO POR CASA ===\n\n" . self::COR_RESET;

        if (empty($this->registrosDisciplinares)) {
            echo self::COR_AVISO . "Nenhum registro disciplinar encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Filtrar apenas ocorrências (excluindo histórico de pontos)
        $ocorrencias = array_filter($this->registrosDisciplinares, function($registro) {
            return isset($registro['aluno_id']);
        });

        if (empty($ocorrencias)) {
            echo self::COR_AVISO . "Nenhuma ocorrência disciplinar registrada.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        // Agrupar por casa
        $porCasa = [];
        foreach ($ocorrencias as $ocorrencia) {
            $alunoId = $ocorrencia['aluno_id'];
            $casa = $this->alunos[$alunoId]['casa'] ?? 'N/D';
            if (!isset($porCasa[$casa])) {
                $porCasa[$casa] = ['positivo' => 0, 'leve' => 0, 'moderada' => 0, 'grave' => 0, 'total_pontos' => 0];
            }
            $porCasa[$casa][$ocorrencia['tipo']]++;
            $porCasa[$casa]['total_pontos'] += $ocorrencia['pontos'];
        }

        // Exibir relatório
        echo "COMPORTAMENTO POR CASA:\n\n";
        foreach ($porCasa as $casa => $dados) {
            echo "{$this->getNomeCasa($casa)}:\n";
            echo " - Comportamentos exemplares: {$dados['positivo']}\n";
            echo " - Infrações leves: {$dados['leve']}\n";
            echo " - Infrações moderadas: {$dados['moderada']}\n";
            echo " - Infrações graves: {$dados['grave']}\n";
            echo " - Saldo de pontos: {$dados['total_pontos']}\n\n";
        }

        echo "\n" . self::COR_SUCESSO . "Pressione Enter para continuar..." . self::COR_RESET;
        fgets(STDIN);
    }

    private function relatorioRankingCasas(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== RANKING DAS CASAS ===\n\n" . self::COR_RESET;

        // Ordenar casas por pontuação
        $ranking = $this->pontuacaoCasas;
        arsort($ranking);

        echo "POSIÇÃO | CASA           | PONTOS\n";
        echo "--------|----------------|--------\n";
        $posicao = 1;
        foreach ($ranking as $sigla => $pontos) {
            echo str_pad($posicao, 8) . " | " . 
                 str_pad($this->getNomeCasa($sigla), 14) . " | " . 
                 $pontos . "\n";
            $posicao++;
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
            'L' => 'Lufa-Lufa',
            'N/D' => 'Não definida'
        ];
        return $casas[$sigla] ?? $sigla;
    }
}
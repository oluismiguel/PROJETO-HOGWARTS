<?php
namespace Src\Model;

use Exception;

class ConviteECadastroDeAlunos
{
    private const COR_TITULO = "\033[1;36m";
    private const COR_SUCESSO = "\033[1;32m";
    private const COR_ERRO = "\033[1;31m";
    private const COR_AVISO = "\033[1;33m";
    private const COR_RESET = "\033[0m";
    private const COR_DESTAQUE = "\033[1;35m";

    private $alunos = [];
    private $convites = [];
    private $arquivoAlunos = __DIR__ . '/../../data/alunos.json';
    private $arquivoConvites = __DIR__ . '/../../data/convites.json';

    public function __construct()
    {
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
                    $this->cadastrarNovoAluno();
                    break;
                case '2':
                    $this->enviarConvites();
                    break;
                case '3':
                    $this->confirmarRecebimento();
                    break;
                case '4':
                    $this->visualizarConvites();
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
        echo "       MÓDULO 1 - CADASTRO E CONVITES\n";
        echo "=============================================\n\n" . self::COR_RESET;

        echo self::COR_DESTAQUE . "MENU:\n" . self::COR_RESET;
        echo "  [1] Cadastrar novo aluno\n";
        echo "  [2] Enviar cartas-convite\n";
        echo "  [3] Confirmar recebimento (aluno)\n";
        echo "  [4] Visualizar convites enviados\n";
        echo "  [0] Voltar ao menu principal\n\n";
    }

    private function lerOpcao(): string
    {
        echo self::COR_DESTAQUE . "Digite sua opção: " . self::COR_RESET;
        return trim(fgets(STDIN));
    }

    private function cadastrarNovoAluno(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== CADASTRO DE NOVO ALUNO ===\n\n" . self::COR_RESET;

        echo "Nome completo: ";
        $nome = trim(fgets(STDIN));

        echo "Data de nascimento (DD/MM/AAAA): ";
        $nascimento = trim(fgets(STDIN));

        echo "Endereço: ";
        $endereco = trim(fgets(STDIN));

        echo "E-mail (opcional): ";
        $email = trim(fgets(STDIN));

        echo "Nome dos pais/responsáveis: ";
        $responsaveis = trim(fgets(STDIN));

        $id = uniqid();
        $this->alunos[$id] = [
            'id' => $id,
            'nome' => $nome,
            'nascimento' => $nascimento,
            'endereco' => $endereco,
            'email' => $email,
            'responsaveis' => $responsaveis,
            'data_cadastro' => date('d/m/Y H:i:s')
        ];

        echo self::COR_SUCESSO . "\nAluno cadastrado com sucesso! ID: {$id}\n" . self::COR_RESET;
        sleep(2);
    }

    private function enviarConvites(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== ENVIO DE CONVITES ===\n\n" . self::COR_RESET;

        if (empty($this->alunos)) {
            echo self::COR_AVISO . "Nenhum aluno cadastrado para enviar convites.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $alunosSemConvite = array_filter($this->alunos, function($aluno) {
            return !isset($this->convites[$aluno['id']]);
        });

        if (empty($alunosSemConvite)) {
            echo self::COR_SUCESSO . "Todos os alunos já receberam seus convites.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "Alunos que receberão convites:\n";
        foreach ($alunosSemConvite as $aluno) {
            echo " - {$aluno['nome']} ({$aluno['nascimento']})\n";
        }

        echo "\n" . self::COR_AVISO . "Deseja enviar os convites? (S/N): " . self::COR_RESET;
        $confirmacao = strtoupper(trim(fgets(STDIN)));

        if ($confirmacao === 'S') {
            foreach ($alunosSemConvite as $aluno) {
                $this->enviarConviteIndividual($aluno);
            }
            echo self::COR_SUCESSO . "\nConvites enviados com sucesso!\n" . self::COR_RESET;
        } else {
            echo self::COR_AVISO . "\nOperação cancelada.\n" . self::COR_RESET;
        }
        sleep(2);
    }

    private function enviarConviteIndividual(array $aluno): void
    {
        $idConvite = uniqid();
        $this->convites[$aluno['id']] = [
            'id' => $idConvite,
            'aluno_id' => $aluno['id'],
            'data_envio' => date('d/m/Y H:i:s'),
            'status' => 'enviado',
            'data_confirmacao' => null,
            'conteudo' => $this->gerarConteudoConvite($aluno)
        ];

        // Aqui seria o local para integrar com um sistema de e-mail ou API de corujas
        // Por enquanto, apenas simulamos o envio
    }

    private function gerarConteudoConvite(array $aluno): string
    {
        $dataEmbarque = date('d/m/Y', strtotime('+1 week'));
        $horaEmbarque = '11:00';

        return "Caro(a) {$aluno['nome']},\n\n"
            . "Temos o prazer de informar que você foi aceito(a) em Hogwarts Escola de Magia e Bruxaria.\n"
            . "Seu embarque no Expresso de Hogwarts está marcado para {$dataEmbarque} às {$horaEmbarque} na Plataforma 9¾.\n\n"
            . "Materiais necessários:\n"
            . "- Uniforme de Hogwarts\n"
            . "- Varinha mágica\n"
            . "- Caldeirão de estanho\n"
            . "- Livros didáticos\n\n"
            . "Por favor, confirme o recebimento desta carta.\n\n"
            . "Atenciosamente,\n"
            . "Alvo Dumbledore\n"
            . "Diretor de Hogwarts";
    }

    private function confirmarRecebimento(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== CONFIRMAÇÃO DE RECEBIMENTO ===\n\n" . self::COR_RESET;

        echo "Informe o ID do aluno: ";
        $alunoId = trim(fgets(STDIN));

        if (!isset($this->alunos[$alunoId])) {
            echo self::COR_ERRO . "\nAluno não encontrado.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        if (!isset($this->convites[$alunoId])) {
            echo self::COR_AVISO . "\nNenhum convite foi enviado para este aluno.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        if ($this->convites[$alunoId]['status'] === 'confirmado') {
            echo self::COR_AVISO . "\nO convite já foi confirmado anteriormente.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        echo "\nAluno: " . $this->alunos[$alunoId]['nome'] . "\n";
        echo "Confirma o recebimento da carta? (S/N): ";
        $confirmacao = strtoupper(trim(fgets(STDIN)));

        if ($confirmacao === 'S') {
            $this->convites[$alunoId]['status'] = 'confirmado';
            $this->convites[$alunoId]['data_confirmacao'] = date('d/m/Y H:i:s');
            echo self::COR_SUCESSO . "\nRecebimento confirmado com sucesso!\n" . self::COR_RESET;
        } else {
            echo self::COR_AVISO . "\nConfirmação cancelada.\n" . self::COR_RESET;
        }
        sleep(2);
    }

    private function visualizarConvites(): void
    {
        $this->limparTela();
        echo self::COR_TITULO . "=== VISUALIZAR CONVITES ===\n\n" . self::COR_RESET;

        if (empty($this->convites)) {
            echo self::COR_AVISO . "Nenhum convite foi enviado ainda.\n" . self::COR_RESET;
            sleep(2);
            return;
        }

        $total = count($this->convites);
        $confirmados = count(array_filter($this->convites, function($convite) {
            return $convite['status'] === 'confirmado';
        }));

        echo "Resumo:\n";
        echo " - Total de convites enviados: {$total}\n";
        echo " - Confirmados: {$confirmados}\n";
        echo " - Pendentes: " . ($total - $confirmados) . "\n\n";

        echo "Detalhes:\n";
        foreach ($this->convites as $convite) {
            $aluno = $this->alunos[$convite['aluno_id']];
            $status = $convite['status'] === 'confirmado' 
                ? self::COR_SUCESSO . 'CONFIRMADO' . self::COR_RESET
                : self::COR_AVISO . 'PENDENTE' . self::COR_RESET;
            
            echo " - {$aluno['nome']} | Enviado: {$convite['data_envio']} | Status: {$status}\n";
        }

        echo "\n" . self::COR_DESTAQUE . "Pressione Enter para continuar..." . self::COR_RESET;
        fgets(STDIN);
    }

    private function carregarDados(): void
    {
        if (file_exists($this->arquivoAlunos)) {
            $this->alunos = json_decode(file_get_contents($this->arquivoAlunos), true) ?: [];
        }

        if (file_exists($this->arquivoConvites)) {
            $this->convites = json_decode(file_get_contents($this->arquivoConvites), true) ?: [];
        }
    }

    private function salvarDados(): void
    {
        file_put_contents($this->arquivoAlunos, json_encode($this->alunos, JSON_PRETTY_PRINT));
        file_put_contents($this->arquivoConvites, json_encode($this->convites, JSON_PRETTY_PRINT));
    }

    private function limparTela(): void
    {
        echo "\033c";
    }
}
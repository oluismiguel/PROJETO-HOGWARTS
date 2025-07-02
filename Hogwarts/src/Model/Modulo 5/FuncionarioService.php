<?php

namespace Hogwarts\Modulo5;

use Hogwarts\Modulo4\Materia;

class FuncionarioService
{
    private array $funcionarios = [];
    private array $horarios = [];
    private array $avaliacoes = [];

    public function cadastrarProfessor(array $dados): Professor
    {
        $professor = new Professor(
            $dados['id'],
            $dados['nome'],
            $dados['email'],
            new \DateTime($dados['data_nascimento']),
            $dados['especialidade'],
            $dados['salario']
        );

        $this->funcionarios[] = $professor;
        return $professor;
    }

    public function cadastrarFuncionarioAdministrativo(array $dados): FuncionarioAdministrativo
    {
        $funcionario = new FuncionarioAdministrativo(
            $dados['id'],
            $dados['nome'],
            $dados['email'],
            new \DateTime($dados['data_nascimento']),
            $dados['cargo'],
            $dados['setor'],
            $dados['salario']
        );

        $this->funcionarios[] = $funcionario;
        return $funcionario;
    }

    public function cadastrarDiretor(array $dados): Diretor
    {
        $diretor = new Diretor(
            $dados['id'],
            $dados['nome'],
            $dados['email'],
            new \DateTime($dados['data_nascimento']),
            $dados['salario']
        );

        $this->funcionarios[] = $diretor;
        return $diretor;
    }

    public function criarHorario(Professor $professor, Materia $materia, string $dia, \DateTime $inicio, \DateTime $fim, string $sala): HorarioAula
    {
        $horario = new HorarioAula($professor, $materia, $dia, $inicio, $fim, $sala);
        
        // Verificar conflitos
        foreach ($this->horarios as $h) {
            if ($horario->conflitaCom($h)) {
                throw new \Exception("Conflito de horário detectado");
            }
        }
        
        $this->horarios[] = $horario;
        return $horario;
    }

    public function avaliarFuncionario(Funcionario $funcionario, \DateTime $periodo): AvaliacaoDesempenho
    {
        $avaliacao = new AvaliacaoDesempenho($funcionario, $periodo);
        $this->avaliacoes[] = $avaliacao;
        return $avaliacao;
    }

    public function getFuncionarios(): array
    {
        return $this->funcionarios;
    }

    public function getHorarios(): array
    {
        return $this->horarios;
    }

    public function getAvaliacoes(): array
    {
        return $this->avaliacoes;
    }

    public function getProfessores(): array
    {
        return array_filter($this->funcionarios, fn($f) => $f instanceof Professor);
    }

    public function getFuncionariosAtivos(): array
    {
        return array_filter($this->funcionarios, fn($f) => $f->isAtivo());
    }

    public function buscarPorCargo(string $cargo): array
    {
        return array_filter($this->funcionarios, fn($f) => $f->getCargo() === $cargo);
    }

    public function gerarRelatorioFolhaPagamento(): array
    {
        $relatorio = [];
        
        foreach ($this->funcionarios as $funcionario) {
            if ($funcionario->isAtivo()) {
                $relatorio[] = [
                    'nome' => $funcionario->getNome(),
                    'cargo' => $funcionario->getCargo(),
                    'departamento' => $funcionario->getDepartamento(),
                    'salario' => $funcionario->getSalario()
                ];
            }
        }
        
        return $relatorio;
    }
}

?>

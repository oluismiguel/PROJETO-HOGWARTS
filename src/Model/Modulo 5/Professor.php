<?php

namespace Hogwarts\Modulo5;

use Hogwarts\Modulo4\Materia;
use Hogwarts\Common\Casa;

class Professor extends Funcionario
{
    private array $materias = [];
    private string $especialidade;
    private ?Casa $casaResponsavel = null;
    private array $horarios = [];

    public function __construct(int $id, string $nome, string $email, \DateTime $dataNascimento, string $especialidade, float $salario)
    {
        parent::__construct($id, $nome, $email, $dataNascimento, 'Professor', 'Acadêmico', $salario);
        $this->especialidade = $especialidade;
    }

    public function adicionarMateria(Materia $materia): void
    {
        $this->materias[] = $materia;
    }

    public function removerMateria(Materia $materia): void
    {
        $this->materias = array_filter($this->materias, function($m) use ($materia) {
            return $m->getCodigo() !== $materia->getCodigo();
        });
    }

    public function definirCasaResponsavel(Casa $casa): void
    {
        $this->casaResponsavel = $casa;
    }

    public function adicionarHorario(string $dia, string $horario, Materia $materia): void
    {
        $this->horarios[] = [
            'dia' => $dia,
            'horario' => $horario,
            'materia' => $materia
        ];
    }

    public function getMaterias(): array
    {
        return $this->materias;
    }

    public function getEspecialidade(): string
    {
        return $this->especialidade;
    }

    public function getCasaResponsavel(): ?Casa
    {
        return $this->casaResponsavel;
    }

    public function getHorarios(): array
    {
        return $this->horarios;
    }

    public function getResponsabilidades(): array
    {
        $responsabilidades = ['Ministrar aulas'];
        
        if ($this->casaResponsavel) {
            $responsabilidades[] = "Chefe da Casa {$this->casaResponsavel->value}";
        }
        
        if (count($this->materias) > 0) {
            $materias = array_map(fn($m) => $m->getNome(), $this->materias);
            $responsabilidades[] = "Lecionar: " . implode(', ', $materias);
        }
        
        return $responsabilidades;
    }
}

?>

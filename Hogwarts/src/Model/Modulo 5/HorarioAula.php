<?php

namespace Hogwarts\Modulo5;

use Hogwarts\Modulo4\Materia;

class HorarioAula
{
    private Professor $professor;
    private Materia $materia;
    private string $diaSemana;
    private \DateTime $horarioInicio;
    private \DateTime $horarioFim;
    private string $sala;

    public function __construct(Professor $professor, Materia $materia, string $diaSemana, \DateTime $inicio, \DateTime $fim, string $sala)
    {
        $this->professor = $professor;
        $this->materia = $materia;
        $this->diaSemana = $diaSemana;
        $this->horarioInicio = $inicio;
        $this->horarioFim = $fim;
        $this->sala = $sala;
    }

    public function getProfessor(): Professor
    {
        return $this->professor;
    }

    public function getMateria(): Materia
    {
        return $this->materia;
    }

    public function getDiaSemana(): string
    {
        return $this->diaSemana;
    }

    public function getHorarioInicio(): \DateTime
    {
        return $this->horarioInicio;
    }

    public function getHorarioFim(): \DateTime
    {
        return $this->horarioFim;
    }

    public function getSala(): string
    {
        return $this->sala;
    }

    public function getDuracao(): \DateInterval
    {
        return $this->horarioInicio->diff($this->horarioFim);
    }

    public function conflitaCom(HorarioAula $outro): bool
    {
        if ($this->diaSemana !== $outro->diaSemana) {
            return false;
        }

        return !($this->horarioFim <= $outro->horarioInicio || $this->horarioInicio >= $outro->horarioFim);
    }
}

?>

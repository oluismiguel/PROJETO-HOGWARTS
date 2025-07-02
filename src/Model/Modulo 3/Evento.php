<?php

namespace Hogwarts\Modulo3;

abstract class Evento
{
    protected string $nome;
    protected \DateTime $dataInicio;
    protected \DateTime $dataFim;
    protected string $status = 'planejado'; 

    public function __construct(string $nome, \DateTime $dataInicio, \DateTime $dataFim)
    {
        $this->nome = $nome;
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function iniciar(): void
    {
        $this->status = 'ativo';
    }

    public function finalizar(): void
    {
        $this->status = 'finalizado';
    }

    public function cancelar(): void
    {
        $this->status = 'cancelado';
    }

    abstract public function calcularPontuacao(): array;
}

?>

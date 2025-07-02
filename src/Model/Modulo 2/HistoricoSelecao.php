<?php

namespace Hogwarts\Modulo2;

use Hogwarts\Common\Casa;
use Hogwarts\Modulo1\Aluno;

class HistoricoSelecao
{
    private Aluno $aluno;
    private Casa $casaSelecionada;
    private array $respostas;
    private \DateTime $dataSelecao;
    private string $metodoSelecao;

    public function __construct(Aluno $aluno, Casa $casa, array $respostas, string $metodo)
    {
        $this->aluno = $aluno;
        $this->casaSelecionada = $casa;
        $this->respostas = $respostas;
        $this->metodoSelecao = $metodo;
        $this->dataSelecao = new \DateTime();
    }

    public function getAluno(): Aluno
    {
        return $this->aluno;
    }

    public function getCasaSelecionada(): Casa
    {
        return $this->casaSelecionada;
    }

    public function getDataSelecao(): \DateTime
    {
        return $this->dataSelecao;
    }
}

?>
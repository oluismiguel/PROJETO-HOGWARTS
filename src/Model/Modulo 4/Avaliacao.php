<?php

namespace Hogwarts\Modulo4;

use Hogwarts\Modulo1\Aluno;

class Avaliacao
{
    private string $tipo; 
    private string $nome;
    private Materia $materia;
    private \DateTime $dataRealizacao;
    private float $notaMaxima;
    private array $notas = []; 
    private string $descricao;

    public function __construct(string $tipo, string $nome, Materia $materia, float $notaMaxima = 10.0)
    {
        $this->tipo = $tipo;
        $this->nome = $nome;
        $this->materia = $materia;
        $this->notaMaxima = $notaMaxima;
        $this->dataRealizacao = new \DateTime();
        $this->descricao = '';
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getMateria(): Materia
    {
        return $this->materia;
    }

    public function getDataRealizacao(): \DateTime
    {
        return $this->dataRealizacao;
    }

    public function getNotaMaxima(): float
    {
        return $this->notaMaxima;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function atribuirNota(int $alunoId, float $nota): void
    {
        if ($nota < 0 || $nota > $this->notaMaxima) {
            throw new \InvalidArgumentException("Nota deve estar entre 0 e {$this->notaMaxima}");
        }
        
        $this->notas[$alunoId] = $nota;
    }

    public function getNota(int $alunoId): ?float
    {
        return $this->notas[$alunoId] ?? null;
    }

    public function getTodasNotas(): array
    {
        return $this->notas;
    }

    public function calcularMediaTurma(): float
    {
        if (empty($this->notas)) {
            return 0.0;
        }

        return array_sum($this->notas) / count($this->notas);
    }
}

?>

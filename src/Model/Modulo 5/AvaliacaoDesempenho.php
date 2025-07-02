<?php

namespace Hogwarts\Modulo5;

class AvaliacaoDesempenho
{
    private Funcionario $funcionario;
    private \DateTime $periodo;
    private array $criterios = [];
    private float $notaFinal = 0.0;
    private string $observacoes = '';

    public function __construct(Funcionario $funcionario, \DateTime $periodo)
    {
        $this->funcionario = $funcionario;
        $this->periodo = $periodo;
    }

    public function getFuncionario(): Funcionario
    {
        return $this->funcionario;
    }

    public function getPeriodo(): \DateTime
    {
        return $this->periodo;
    }

    public function adicionarCriterio(string $nome, float $nota, float $peso = 1.0): void
    {
        $this->criterios[] = [
            'nome' => $nome,
            'nota' => $nota,
            'peso' => $peso
        ];
        $this->calcularNotaFinal();
    }

    private function calcularNotaFinal(): void
    {
        if (empty($this->criterios)) {
            $this->notaFinal = 0.0;
            return;
        }

        $somaNotas = 0;
        $somaPesos = 0;

        foreach ($this->criterios as $criterio) {
            $somaNotas += $criterio['nota'] * $criterio['peso'];
            $somaPesos += $criterio['peso'];
        }

        $this->notaFinal = $somaPesos > 0 ? $somaNotas / $somaPesos : 0.0;
    }

    public function getNotaFinal(): float
    {
        return $this->notaFinal;
    }

    public function getCriterios(): array
    {
        return $this->criterios;
    }

    public function setObservacoes(string $observacoes): void
    {
        $this->observacoes = $observacoes;
    }

    public function getObservacoes(): string
    {
        return $this->observacoes;
    }

    public function getClassificacao(): string
    {
        return match(true) {
            $this->notaFinal >= 9.0 => 'Excepcional',
            $this->notaFinal >= 8.0 => 'Muito Bom',
            $this->notaFinal >= 7.0 => 'Bom',
            $this->notaFinal >= 6.0 => 'Satisfatório',
            $this->notaFinal >= 5.0 => 'Regular',
            default => 'Insatisfatório'
        };
    }
}

?>

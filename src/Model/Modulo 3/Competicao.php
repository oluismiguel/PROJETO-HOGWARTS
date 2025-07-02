<?php

namespace Hogwarts\Modulo3;

use Hogwarts\Common\Casa;

class Competicao
{
    private string $tipo;
    private Casa $casaA;
    private Casa $casaB;
    private ?Casa $vencedor = null;
    private int $pontosVencedor = 0;
    private int $pontosPerdedor = 0;
    private \DateTime $dataRealizacao;
    private array $detalhes = [];

    public function __construct(string $tipo, Casa $casaA, Casa $casaB)
    {
        $this->tipo = $tipo;
        $this->casaA = $casaA;
        $this->casaB = $casaB;
        $this->dataRealizacao = new \DateTime();
    }

    public function definirResultado(Casa $vencedor, int $pontosVencedor, int $pontosPerdedor = 0): void
    {
        if ($vencedor !== $this->casaA && $vencedor !== $this->casaB) {
            throw new \InvalidArgumentException('Casa vencedora deve ser uma das participantes');
        }

        $this->vencedor = $vencedor;
        $this->pontosVencedor = $pontosVencedor;
        $this->pontosPerdedor = $pontosPerdedor;
    }

    public function adicionarDetalhe(string $chave, $valor): void
    {
        $this->detalhes[$chave] = $valor;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getVencedor(): ?Casa
    {
        return $this->vencedor;
    }

    public function getPontosVencedor(): int
    {
        return $this->pontosVencedor;
    }

    public function getCasas(): array
    {
        return [$this->casaA, $this->casaB];
    }
}

?>

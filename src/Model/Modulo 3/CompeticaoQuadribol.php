<?php

namespace Hogwarts\Modulo3;

use Hogwarts\Common\Casa;

class CompeticaoQuadribol extends Competicao
{
    private int $placarCasaA = 0;
    private int $placarCasaB = 0;
    private bool $pomomCapturado = false;
    private ?Casa $capturouPomo = null;

    public function __construct(Casa $casaA, Casa $casaB)
    {
        parent::__construct('quadribol', $casaA, $casaB);
    }

    public function adicionarPonto(Casa $casa, int $pontos = 10): void
    {
        if ($casa === $this->casaA) {
            $this->placarCasaA += $pontos;
        } elseif ($casa === $this->casaB) {
            $this->placarCasaB += $pontos;
        }
    }

    public function capturarPomo(Casa $casa): void
    {
        $this->pomomCapturado = true;
        $this->capturouPomo = $casa;
        $this->adicionarPonto($casa, 150);
        
        $vencedor = $this->placarCasaA > $this->placarCasaB ? $this->casaA : $this->casaB;
        $pontosVencedor = max($this->placarCasaA, $this->placarCasaB);
        $pontosPerdedor = min($this->placarCasaA, $this->placarCasaB);
        
        $this->definirResultado($vencedor, $pontosVencedor, $pontosPerdedor);
    }

    public function getPlacar(): array
    {
        return [
            $this->casaA->value => $this->placarCasaA,
            $this->casaB->value => $this->placarCasaB
        ];
    }
}

?>

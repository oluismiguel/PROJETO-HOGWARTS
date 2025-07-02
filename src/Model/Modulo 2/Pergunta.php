<?php

namespace Hogwarts\Modulo2;

class Pergunta
{
    private string $texto;
    private array $opcoes;
    private array $pesos;

    public function __construct(string $texto, array $opcoes, array $pesos)
    {
        $this->texto = $texto;
        $this->opcoes = $opcoes;
        $this->pesos = $pesos;
    }

    public function getTexto(): string
    {
        return $this->texto;
    }

    public function getOpcoes(): array
    {
        return $this->opcoes;
    }

    public function getPeso(int $opcaoSelecionada): array
    {
        return $this->pesos[$opcaoSelecionada] ?? [];
    }
}

?>

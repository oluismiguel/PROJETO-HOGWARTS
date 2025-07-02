<?php

namespace Hogwarts\Modulo4;

class Materia
{
    private string $nome;
    private string $codigo;
    private int $cargaHoraria;
    private string $descricao;
    private int $anoMinimo;

    public function __construct(string $nome, string $codigo, int $cargaHoraria, int $anoMinimo = 1)
    {
        $this->nome = $nome;
        $this->codigo = $codigo;
        $this->cargaHoraria = $cargaHoraria;
        $this->anoMinimo = $anoMinimo;
        $this->descricao = '';
    }

    public function getNome(): string 
    { 
        return $this->nome; 
    }

    public function getCodigo(): string 
    { 
        return $this->codigo; 
    }

    public function getCargaHoraria(): int 
    { 
        return $this->cargaHoraria; 
    }

    public function getAnoMinimo(): int 
    { 
        return $this->anoMinimo; 
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }
}

?>

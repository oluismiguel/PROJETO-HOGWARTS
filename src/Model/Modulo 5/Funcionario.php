<?php

namespace Hogwarts\Modulo5;

use Hogwarts\Common\Pessoa;

abstract class Funcionario extends Pessoa
{
    protected string $cargo;
    protected string $departamento;
    protected float $salario;
    protected \DateTime $dataAdmissao;
    protected bool $ativo = true;

    public function __construct(int $id, string $nome, string $email, \DateTime $dataNascimento, string $cargo, string $departamento, float $salario)
    {
        parent::__construct($id, $nome, $email, $dataNascimento);
        $this->cargo = $cargo;
        $this->departamento = $departamento;
        $this->salario = $salario;
        $this->dataAdmissao = new \DateTime();
    }

    public function getCargo(): string 
    { 
        return $this->cargo; 
    }

    public function getDepartamento(): string 
    { 
        return $this->departamento; 
    }

    public function getSalario(): float 
    { 
        return $this->salario; 
    }

    public function isAtivo(): bool 
    { 
        return $this->ativo; 
    }

    public function desativar(): void
    {
        $this->ativo = false;
    }

    public function reativar(): void
    {
        $this->ativo = true;
    }

    abstract public function getResponsabilidades(): array;
}

?>

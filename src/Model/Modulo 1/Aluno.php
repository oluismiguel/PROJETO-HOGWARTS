<?php

namespace Hogwarts\Modulo1;

use Hogwarts\Common\Pessoa;
use Hogwarts\Common\Casa;

class Aluno extends Pessoa
{
    private ?Casa $casa = null;
    private array $responsaveis = [];
    private string $status = 'pendente'; 
    private string $anoLetivo;

    public function __construct(int $id, string $nome, string $email, \DateTime $dataNascimento, string $anoLetivo)
    {
        parent::__construct($id, $nome, $email, $dataNascimento);
        $this->anoLetivo = $anoLetivo;
    }

    public function setCasa(Casa $casa): void
    {
        $this->casa = $casa;
    }

    public function getCasa(): ?Casa
    {
        return $this->casa;
    }

    public function adicionarResponsavel(Responsavel $responsavel): void
    {
        $this->responsaveis[] = $responsavel;
    }

    public function getResponsaveis(): array
    {
        return $this->responsaveis;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function ativar(): void
    {
        $this->status = 'ativo';
    }

    public function desativar(): void
    {
        $this->status = 'inativo';
    }

    public function getAnoLetivo(): string
    {
        return $this->anoLetivo;
    }
}

?>

<?php

namespace Hogwarts\Modulo1;

class Responsavel
{
    private string $nome;
    private string $email;
    private string $telefone;
    private string $parentesco;

    public function __construct(string $nome, string $email, string $telefone, string $parentesco)
    {
        $this->nome = $nome;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->parentesco = $parentesco;
    }

    public function getNome(): string 
    { 
        return $this->nome; 
    }

    public function getEmail(): string 
    { 
        return $this->email; 
    }

    public function getTelefone(): string 
    { 
        return $this->telefone; 
    }

    public function getParentesco(): string 
    { 
        return $this->parentesco; 
    }
}

?>
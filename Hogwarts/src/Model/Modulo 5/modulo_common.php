<?php

namespace Hogwarts\Common;

interface PessoaInterface
{
    public function getNome(): string;
    public function getEmail(): string;
    public function getId(): int;
}

abstract class Pessoa implements PessoaInterface
{
    protected int $id;
    protected string $nome;
    protected string $email;
    protected \DateTime $dataNascimento;

    public function __construct(int $id, string $nome, string $email, \DateTime $dataNascimento)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->dataNascimento = $dataNascimento;
    }

    public function getId(): int 
    { 
        return $this->id; 
    }

    public function getNome(): string 
    { 
        return $this->nome; 
    }

    public function getEmail(): string 
    { 
        return $this->email; 
    }

    public function getDataNascimento(): \DateTime 
    { 
        return $this->dataNascimento; 
    }
}

enum Casa: string
{
    case GRIFINORIA = 'grifinoria';
    case SONSERINA = 'sonserina';
    case CORVINAL = 'corvinal';
    case LUFA_LUFA = 'lufa_lufa';

    public function getCor(): string
    {
        return match($this) {
            self::GRIFINORIA => 'vermelho',
            self::SONSERINA => 'verde',
            self::CORVINAL => 'azul',
            self::LUFA_LUFA => 'amarelo'
        };
    }
}

?>

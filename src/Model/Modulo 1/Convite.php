<?php

namespace Hogwarts\Modulo1;

class Convite
{
    private string $token;
    private string $emailDestino;
    private string $nomeAluno;
    private \DateTime $dataEnvio;
    private \DateTime $dataExpiracao;
    private bool $utilizado = false;

    public function __construct(string $emailDestino, string $nomeAluno)
    {
        $this->token = bin2hex(random_bytes(32));
        $this->emailDestino = $emailDestino;
        $this->nomeAluno = $nomeAluno;
        $this->dataEnvio = new \DateTime();
        $this->dataExpiracao = new \DateTime('+7 days');
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isValido(): bool
    {
        return !$this->utilizado && $this->dataExpiracao > new \DateTime();
    }

    public function utilizar(): void
    {
        $this->utilizado = true;
    }
}

?>
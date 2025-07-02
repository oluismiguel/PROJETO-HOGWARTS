<?php

namespace Hogwarts\Modulo1;

class ConviteService
{
    private array $convites = [];

    public function enviarConvite(string $email, string $nomeAluno): Convite
    {
        $convite = new Convite($email, $nomeAluno);
        $this->convites[] = $convite;
        
        $this->enviarEmail($email, $convite->getToken());
        
        return $convite;
    }

    public function validarConvite(string $token): bool
    {
        foreach ($this->convites as $convite) {
            if ($convite->getToken() === $token && $convite->isValido()) {
                return true;
            }
        }
        return false;
    }

    public function utilizarConvite(string $token): bool
    {
        foreach ($this->convites as $convite) {
            if ($convite->getToken() === $token && $convite->isValido()) {
                $convite->utilizar();
                return true;
            }
        }
        return false;
    }

    private function enviarEmail(string $email, string $token): void
    {
    
        echo "Email enviado para {$email} com token: {$token}\n";
    }
}

?>

<?php

namespace Hogwarts\Modulo5;

class Diretor extends Funcionario
{
    private array $poderes = [];

    public function __construct(int $id, string $nome, string $email, \DateTime $dataNascimento, float $salario)
    {
        parent::__construct($id, $nome, $email, $dataNascimento, 'Diretor', 'Diretoria', $salario);
        $this->inicializarPoderes();
    }

    private function inicializarPoderes(): void
    {
        $this->poderes = [
            'Admitir/Demitir funcionários',
            'Autorizar suspensões',
            'Modificar regras escolares',
            'Convocar reuniões',
            'Representar a escola'
        ];
    }

    public function getPoderes(): array
    {
        return $this->poderes;
    }

    public function getResponsabilidades(): array
    {
        return [
            'Administração geral da escola',
            'Tomada de decisões estratégicas',
            'Supervisão de todos os departamentos',
            'Relacionamento com autoridades mágicas',
            'Manutenção da ordem e disciplina'
        ];
    }
}

?>

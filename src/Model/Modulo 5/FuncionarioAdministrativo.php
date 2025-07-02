<?php

namespace Hogwarts\Modulo5;

class FuncionarioAdministrativo extends Funcionario
{
    private array $permissoes = [];
    private string $setor;

    public function __construct(int $id, string $nome, string $email, \DateTime $dataNascimento, string $cargo, string $setor, float $salario)
    {
        parent::__construct($id, $nome, $email, $dataNascimento, $cargo, 'Administrativo', $salario);
        $this->setor = $setor;
    }

    public function adicionarPermissao(string $permissao): void
    {
        if (!in_array($permissao, $this->permissoes)) {
            $this->permissoes[] = $permissao;
        }
    }

    public function removerPermissao(string $permissao): void
    {
        $this->permissoes = array_filter($this->permissoes, fn($p) => $p !== $permissao);
    }

    public function temPermissao(string $permissao): bool
    {
        return in_array($permissao, $this->permissoes);
    }

    public function getSetor(): string
    {
        return $this->setor;
    }

    public function getPermissoes(): array
    {
        return $this->permissoes;
    }

    public function getResponsabilidades(): array
    {
        return match($this->cargo) {
            'Zelador' => ['Limpeza', 'Manutenção', 'Segurança'],
            'Bibliotecário' => ['Organização de livros', 'Atendimento', 'Controle de empréstimos'],
            'Enfermeiro' => ['Primeiros socorros', 'Cuidados médicos', 'Controle de medicamentos'],
            'Secretário' => ['Documentação', 'Atendimento', 'Organização administrativa'],
            default => ['Atividades administrativas']
        };
    }
}

?>

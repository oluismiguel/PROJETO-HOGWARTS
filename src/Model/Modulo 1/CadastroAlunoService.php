<?php

namespace Hogwarts\Modulo1;

class CadastroAlunoService
{
    public function cadastrarAluno(array $dados): Aluno
    {
        $aluno = new Aluno(
            $dados['id'],
            $dados['nome'],
            $dados['email'],
            new \DateTime($dados['data_nascimento']),
            $dados['ano_letivo']
        );

        if (isset($dados['responsaveis'])) {
            foreach ($dados['responsaveis'] as $resp) {
                $responsavel = new Responsavel(
                    $resp['nome'],
                    $resp['email'],
                    $resp['telefone'],
                    $resp['parentesco']
                );
                $aluno->adicionarResponsavel($responsavel);
            }
        }

        return $aluno;
    }

    public function validarDados(array $dados): array
    {
        $erros = [];

        if (empty($dados['nome'])) {
            $erros[] = 'Nome é obrigatório';
        }

        if (empty($dados['email']) || !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Email válido é obrigatório';
        }

        if (empty($dados['data_nascimento'])) {
            $erros[] = 'Data de nascimento é obrigatória';
        }

        return $erros;
    }
}

?>

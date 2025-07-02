<?php

namespace Hogwarts\Modulo2;

use Hogwarts\Common\Casa;
use Hogwarts\Modulo1\Aluno;

class ChapeuSeletor implements SeletorInterface
{
    private array $perguntas = [];

    public function __construct()
    {
        $this->inicializarPerguntas();
    }

    public function selecionar(Aluno $aluno, array $respostas = []): Casa
    {
        if (empty($respostas)) {
           
            $casas = Casa::cases();
            return $casas[array_rand($casas)];
        }

        $pontuacao = [
            Casa::GRIFINORIA->value => 0,
            Casa::SONSERINA->value => 0,
            Casa::CORVINAL->value => 0,
            Casa::LUFA_LUFA->value => 0
        ];

        foreach ($respostas as $index => $resposta) {
            if (isset($this->perguntas[$index])) {
                $pesos = $this->perguntas[$index]->getPeso($resposta);
                foreach ($pesos as $casa => $peso) {
                    $pontuacao[$casa] += $peso;
                }
            }
        }


        $casaSelecionada = array_search(max($pontuacao), $pontuacao);
        
        foreach (Casa::cases() as $casa) {
            if ($casa->value === $casaSelecionada) {
                return $casa;
            }
        }


        return Casa::GRIFINORIA;
    }

    public function getPerguntas(): array
    {
        return $this->perguntas;
    }

    private function inicializarPerguntas(): void
    {
        $this->perguntas = [
            new Pergunta(
                'Qual característica você mais valoriza?',
                ['Coragem', 'Ambição', 'Sabedoria', 'Lealdade'],
                [
                    0 => ['grifinoria' => 3, 'sonserina' => 0, 'corvinal' => 1, 'lufa_lufa' => 1],
                    1 => ['grifinoria' => 0, 'sonserina' => 3, 'corvinal' => 1, 'lufa_lufa' => 0],
                    2 => ['grifinoria' => 1, 'sonserina' => 1, 'corvinal' => 3, 'lufa_lufa' => 0],
                    3 => ['grifinoria' => 1, 'sonserina' => 0, 'corvinal' => 0, 'lufa_lufa' => 3]
                ]
            ),
            new Pergunta(
                'Em uma situação difícil, você prefere?',
                ['Enfrentar de frente', 'Planejar estrategicamente', 'Buscar conhecimento', 'Pedir ajuda aos amigos'],
                [
                    0 => ['grifinoria' => 3, 'sonserina' => 1, 'corvinal' => 0, 'lufa_lufa' => 0],
                    1 => ['grifinoria' => 0, 'sonserina' => 3, 'corvinal' => 2, 'lufa_lufa' => 0],
                    2 => ['grifinoria' => 0, 'sonserina' => 0, 'corvinal' => 3, 'lufa_lufa' => 1],
                    3 => ['grifinoria' => 1, 'sonserina' => 0, 'corvinal' => 0, 'lufa_lufa' => 3]
                ]
            ),
            new Pergunta(
                'Seu maior medo é?',
                ['Fracassar', 'Ser ignorado', 'Ignorância', 'Solidão'],
                [
                    0 => ['grifinoria' => 2, 'sonserina' => 2, 'corvinal' => 1, 'lufa_lufa' => 0],
                    1 => ['grifinoria' => 1, 'sonserina' => 3, 'corvinal' => 0, 'lufa_lufa' => 1],
                    2 => ['grifinoria' => 0, 'sonserina' => 1, 'corvinal' => 3, 'lufa_lufa' => 0],
                    3 => ['grifinoria' => 1, 'sonserina' => 0, 'corvinal' => 1, 'lufa_lufa' => 3]
                ]
            )
        ];
    }
}

?>

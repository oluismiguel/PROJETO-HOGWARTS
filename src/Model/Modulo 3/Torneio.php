<?php

namespace Hogwarts\Modulo3;

use Hogwarts\Common\Casa;

class Torneio extends Evento
{
    private array $competicoes = [];
    private array $participantes = [];
    private array $pontuacaoTotal = [];

    public function adicionarCompeticao(Competicao $competicao): void
    {
        $this->competicoes[] = $competicao;
    }

    public function adicionarParticipante($participante): void
    {
        $this->participantes[] = $participante;
    }

    public function getCompeticoes(): array
    {
        return $this->competicoes;
    }

    public function calcularPontuacao(): array
    {
        $this->pontuacaoTotal = [
            Casa::GRIFINORIA->value => 0,
            Casa::SONSERINA->value => 0,
            Casa::CORVINAL->value => 0,
            Casa::LUFA_LUFA->value => 0
        ];

        foreach ($this->competicoes as $competicao) {
            if ($competicao->getVencedor()) {
                $casa = $competicao->getVencedor()->value;
                $this->pontuacaoTotal[$casa] += $competicao->getPontosVencedor();
            }
        }

        return $this->pontuacaoTotal;
    }

    public function getCasaVencedora(): ?Casa
    {
        if ($this->status !== 'finalizado') {
            return null;
        }

        $pontuacao = $this->calcularPontuacao();
        $casaVencedora = array_search(max($pontuacao), $pontuacao);
        
        foreach (Casa::cases() as $casa) {
            if ($casa->value === $casaVencedora) {
                return $casa;
            }
        }

        return null;
    }
}

?>

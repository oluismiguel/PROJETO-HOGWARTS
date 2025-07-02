<?php


namespace Hogwarts\Modulo3;

use Hogwarts\Common\Casa;

class RankingService
{
    public function calcularRankingGeral(array $torneios): array
    {
        $ranking = [
            Casa::GRIFINORIA->value => 0,
            Casa::SONSERINA->value => 0,
            Casa::CORVINAL->value => 0,
            Casa::LUFA_LUFA->value => 0
        ];

        foreach ($torneios as $torneio) {
            if ($torneio instanceof Torneio && $torneio->getStatus() === 'finalizado') {
                $pontuacao = $torneio->calcularPontuacao();
                foreach ($pontuacao as $casa => $pontos) {
                    $ranking[$casa] += $pontos;
                }
            }
        }

        arsort($ranking);
        
        return $ranking;
    }

    public function getCasaLider(array $torneios): ?Casa
    {
        $ranking = $this->calcularRankingGeral($torneios);
        $casaLider = array_key_first($ranking);
        
        foreach (Casa::cases() as $casa) {
            if ($casa->value === $casaLider) {
                return $casa;
            }
        }
        
        return null;
    }
}

?>

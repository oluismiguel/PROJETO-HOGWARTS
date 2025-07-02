<?php

namespace Hogwarts\Modulo3;

use Hogwarts\Common\Casa;

class TorneioService
{
    private array $torneios = [];

    public function criarTorneio(string $nome, \DateTime $inicio, \DateTime $fim): Torneio
    {
        $torneio = new Torneio($nome, $inicio, $fim);
        $this->torneios[] = $torneio;
        return $torneio;
    }

    public function criarCompeticao(string $tipo, Casa $casaA, Casa $casaB): Competicao
    {
        return match($tipo) {
            'quadribol' => new CompeticaoQuadribol($casaA, $casaB),
            default => new Competicao($tipo, $casaA, $casaB)
        };
    }

    public function organizarTorneioCompleto(string $nome, \DateTime $inicio, \DateTime $fim): Torneio
    {
        $torneio = $this->criarTorneio($nome, $inicio, $fim);
        
            $casas = Casa::cases();
        $tipos = ['quadribol', 'duelo', 'conhecimento'];
        
        for ($i = 0; $i < count($casas); $i++) {
            for ($j = $i + 1; $j < count($casas); $j++) {
                foreach ($tipos as $tipo) {
                    $competicao = $this->criarCompeticao($tipo, $casas[$i], $casas[$j]);
                    $torneio->adicionarCompeticao($competicao);
                }
            }
        }
        
        return $torneio;
    }

    public function getTorneios(): array
    {
        return $this->torneios;
    }

    public function getTorneiosAtivos(): array
    {
        return array_filter($this->torneios, function($torneio) {
            return $torneio->getStatus() === 'ativo';
        });
    }

    public function simularCompeticao(Competicao $competicao): void
    {
        $casas = $competicao->getCasas();
        $vencedor = $casas[array_rand($casas)];
        $pontos = rand(50, 200);
        
        $competicao->definirResultado($vencedor, $pontos);
    }
}

?>

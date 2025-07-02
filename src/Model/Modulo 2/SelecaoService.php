<?php

namespace Hogwarts\Modulo2;

use Hogwarts\Common\Casa;
use Hogwarts\Modulo1\Aluno;

class SelecaoService
{
    private SeletorInterface $seletor;
    private array $historico = [];

    public function __construct(SeletorInterface $seletor)
    {
        $this->seletor = $seletor;
    }

    public function realizarSelecao(Aluno $aluno, array $respostas = []): Casa
    {
        $casa = $this->seletor->selecionar($aluno, $respostas);
        $aluno->setCasa($casa);
        
        $this->historico[] = new HistoricoSelecao(
            $aluno, 
            $casa, 
            $respostas, 
            get_class($this->seletor)
        );
        
        return $casa;
    }

    public function setSeletor(SeletorInterface $seletor): void
    {
        $this->seletor = $seletor;
    }

    public function getHistorico(): array
    {
        return $this->historico;
    }

    public function reselecionar(Aluno $aluno, array $novasRespostas): Casa
    {
    
        return $this->realizarSelecao($aluno, $novasRespostas);
    }
}

?>

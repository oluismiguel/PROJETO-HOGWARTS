<?php

namespace Hogwarts\Modulo2;

use Hogwarts\Common\Casa;
use Hogwarts\Modulo1\Aluno;

class SelecaoManual implements SeletorInterface
{
    public function selecionar(Aluno $aluno, array $respostas = []): Casa
    {
   
        if (isset($respostas['casa_escolhida'])) {
            return Casa::from($respostas['casa_escolhida']);
        }
        
        return Casa::GRIFINORIA; 
    }
}

?>

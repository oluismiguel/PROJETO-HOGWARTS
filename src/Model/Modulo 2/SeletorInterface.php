<?php

namespace Hogwarts\Modulo2;

use Hogwarts\Common\Casa;
use Hogwarts\Modulo1\Aluno;

interface SeletorInterface
{
    public function selecionar(Aluno $aluno, array $respostas = []): Casa;
}

?>
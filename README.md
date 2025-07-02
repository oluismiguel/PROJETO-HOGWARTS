Sistema de Gestão de Hogwarts
Este projeto implementa um sistema de gestão para a Escola de Magia e Bruxaria de Hogwarts, desenvolvido em PHP. Ele é composto por diversos módulos que gerenciam diferentes aspectos da vida acadêmica e social dos alunos e professores, tudo acessível através de um menu principal.

Estrutura do Projeto
O coração da aplicação é o arquivo MenuPrincipal.php, que atua como o ponto de entrada principal (app.php). Este menu centralizado permite a navegação entre os seguintes módulos:

Convite e Cadastro de Alunos (ConviteECadastroDeAlunos.php):

Gerencia o processo de convite e registro de novos alunos.

Permite cadastrar novos alunos, enviar cartas-convite, confirmar o recebimento dos convites e visualizar o status dos convites enviados.

Seleção de Casas (SelecaoDeCasas.php):

Responsável por registrar as características dos alunos e realizar a seleção para uma das quatro casas de Hogwarts (Grifinória, Sonserina, Corvinal, Lufa-Lufa).

Permite consultar a distribuição atual de alunos por casa.

Gerenciamento de Torneios (GerenciamentoDeTorneios.php):

Controla a criação e edição de torneios e competições.

Funcionalidades incluem gerenciar desafios, inscrições de alunos, registrar resultados e visualizar rankings (de casas e individuais).

Controle Acadêmico e Disciplinar (ControleAcademicoDisciplinar.php):

Este módulo lida com o registro de notas acadêmicas e ocorrências disciplinares.

Permite gerenciar a pontuação das casas, visualizar o boletim dos alunos e gerar relatórios acadêmicos e disciplinares.

Gerenciamento de Professores e Funcionários (GerenciamentoProfessores.php):

Gerencia o cadastro de professores e funcionários.

Permite associar professores a disciplinas, alocá-los em turmas e gerenciar seus cronogramas de aulas.

É possível visualizar todos os professores cadastrados e suas informações.

Sistema de Alertas (SistemaAlertas.php):

Permite o envio e gerenciamento de notificações e alertas para diferentes tipos de usuários (alunos, professores, administradores) e casas.

Possui opções para ver o histórico de notificações e agendar alertas (para administradores).

Funcionamento
O sistema opera via linha de comando, apresentando menus interativos para o usuário. Os dados são persistidos em arquivos JSON localizados na pasta data/.

O fluxo principal da aplicação é controlado pela classe MenuPrincipal, que exibe um menu com as opções para acessar cada módulo. Ao selecionar uma opção, o sistema instancia a classe correspondente ao módulo e executa sua lógica principal, que por sua vez, também apresenta um menu específico com suas funcionalidades.

PHP

// Exemplo de como a aplicação seria iniciada (app.php)
require 'vendor/autoload.php'; // Se estiver usando Composer
use Src\Model\MenuPrincipal;

$app = new MenuPrincipal();
$app->executar();
Este app.php iniciaria a aplicação, chamando o método executar() da classe MenuPrincipal, que iniciaria o loop do menu principal e o sistema de gestão.

Requisitos
PHP (versão compatível com a sintaxe utilizada).

Extensão json do PHP habilitada (geralmente vem por padrão).

Acesso de escrita à pasta data/ para persistência dos dados.

Como Executar
Certifique-se de ter o PHP instalado em seu sistema.

Navegue até o diretório raiz do projeto no terminal.

Execute o comando: php app.php

O sistema apresentará o menu principal, e você poderá interagir com os diferentes módulos.
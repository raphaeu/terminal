#!/usr/bin/php
<?php
/**
 * Created by PhpStorm.
 * User: raphaeu
 * Date: 04/02/19
 * Time: 17:27
 *
 * chmod +x arquivo.php
 * lh -s arquivo.php /usr/bin/arquivo
 */

use raphaeu\Parameter;
use raphaeu\Terminal;
require __DIR__.'/../vendor/autoload.php';

$logo="
  ___  ___  ____ 
 / __)/ __)(  _ \
( (__( (__  )   /
 \___)\___)(_)\_)
COMANDO CUSTOMIZADO
      RAFAEL
";

$terminal = new Terminal('ccr', '1.1', 'Rafael Aguiar <raphaeu.aguiar@gmail.com>', $logo);


# PASSANDO UMA FUNÇÃO COMO PARAMETRO
$terminal->add(new Parameter(['-l','--list'], 'Lista as coisa da pasta', function(){echo shell_exec('ls -la');}));

# SEM FUNÇÃO DE PARAMETRO
$terminal->add(new Parameter(['-p'], 'Comando P', 'function1'));
$terminal->add(new Parameter(['-f'], 'comando F'), true);
$terminal->add(new Parameter(['-g'], 'Commndo G'));

# INICIANDO
$terminal->run();


echo $terminal->getValue('-p');


function function1()
{
    echo "test function 1";
}


# TARGET DA AÇÃO
echo "Targets";
print_r($terminal->getTarget());


exit;

## FORMAS DE USAR
#################

# DIRETO

if ($terminal->isset('-p'))
{
    # EXECUTA O COMANDO
    echo 'PARAMETRO CASO EXISTA:'.$terminal->getValue('-p');
}

# FOREACH
foreach ($terminal->getParameters() as $par => $parameter)
{
    switch ($par)
    {
        case '-p':
            # EXECUTA O COMANDO
            echo 'PARAMETRO CASO EXISTA:'.$terminal->getValue('-p');
            break;
    }

}



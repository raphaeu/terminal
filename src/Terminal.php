<?php

/**
 * Created by PhpStorm.
 * User: raphaeu
 * Date: 28/02/19
 * Time: 11:38
 */

namespace raphaeu;

class Terminal
{

    private $values = [];
    private $parameters = [];
    private $target;
    private $version;
    private $createBy;
    private $name;

    function __construct($name, $version='1.0',$createBy=null, $logo=null)
    {
        $this->name = $name;
        $this->version = $version;
        $this->createBy = $createBy;
        $this->logo = $logo;
        $this->add(new Parameter(['--help', '-h'], 'Exibe ajuda do comando', function (){$this->getHelp();}));
    }

    public function getHelp()
    {


        echo $this->logo;
        echo $this->name.' '.Colorize::green().$this->version.Colorize::clear().' ';
        echo PHP_EOL;
        echo PHP_EOL;
        echo Colorize::yellow()."Forma de usar:".Colorize::clear();
        echo PHP_EOL;
        echo "   $this->name [arquivo] [parametros]";
        echo PHP_EOL;
        echo "   php $this->name [arquivo] [parametros]";
        echo PHP_EOL;
        echo PHP_EOL;
        echo Colorize::yellow()."Parametros:".Colorize::clear();
        echo PHP_EOL;

        foreach ($this->parameters as $parameter)
        {
            echo "   ". Colorize::green().implode(" ",$parameter->getItems()) .Colorize::clear(). " \t" . $parameter->getDescription();
            echo PHP_EOL;
        }

    }

    public function add(Parameter $parameter)
    {
        $this->parameters[] = $parameter;
    }

    public function getTarget()
    {
        return $this->target;

    }

    public function getParameters()
    {
        return $this->values;
    }

    public function isset($parameter)
    {
        return isset($this->values[$parameter])?true:false;
    }

    public function getValue($parameter)
    {
        return isset($this->values[$parameter])?$this->values[$parameter]->getValue():null;
    }

    public function run()
    {
        global $argv, $argc;

        $this->target = @$argv[1][0]!='-'?@$argv[1]:'';

        if ($argc)
        {
            foreach ($this->parameters as $parameter)
            {
                foreach ($parameter->getItems() as $item)
                {
                    if (in_array($item, $argv))
                    {
                        $parameter->setValue(isset($argv[array_search($item, $argv)+1])?$argv[array_search($item, $argv)+1]:'');
                        if (!is_null($parameter->function))$parameter->run();
                        $this->values[$item] = $parameter;
                    }
                }
            }
        }
    }
}
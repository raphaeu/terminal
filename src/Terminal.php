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
    private $description = '';

    function __construct($name, $version='1.0',$createBy=null, $logo=null)
    {
        $this->name = $name;
        $this->version = $version;
        $this->createBy = $createBy;
        $this->logo = $logo;
        $this->add(new Parameter(['--help', '-h'], 'Exibe ajuda do comando', function (){$this->showHelp();}));
    }

    private function getLogo()
    {
        echo Colorize::blue();
        echo $this->logo;
        echo Colorize::clear();
        echo PHP_EOL;
        echo Colorize::bold().$this->name.Colorize::clear().' '.Colorize::green().$this->version.Colorize::clear().' '. Colorize::white().$this->createBy.Colorize::clear();
        echo PHP_EOL;
        echo $this->description;
        echo PHP_EOL;
        echo PHP_EOL;
    }

    public function showHelp($msgErro = null)
    {
        $this->getLogo();
        if ($msgErro)
        {
            $this->showError($msgErro);
        }
        $this->getHelp();
        exit;
    }

    public function showError($msgErro)
    {
        echo PHP_EOL;
        echo Colorize::bold().Colorize::red(1)."Erro !!".Colorize::clear()." ".$msgErro;
        echo PHP_EOL;

    }
    private function getHelp()
    {
        echo PHP_EOL;
        echo Colorize::yellow().Colorize::bold()."Forma de usar:".Colorize::clear();
        echo PHP_EOL;
        echo "   $this->name [".Colorize::blue().Colorize::bold()."arquivo".Colorize::clear()."] [".Colorize::blue().Colorize::bold()."parametros".Colorize::clear()."]";
        echo PHP_EOL;
        echo "   php $this->name [".Colorize::blue().Colorize::bold()."arquivo".Colorize::clear()."] [".Colorize::blue().Colorize::bold()."parametros".Colorize::clear()."]";
        echo PHP_EOL;
        echo PHP_EOL;
        echo Colorize::yellow().Colorize::bold()."Parametros:".Colorize::clear();
        echo PHP_EOL;

        foreach ($this->parameters as $parameter)
        {
            $targets = $parameter->getTarget();

            echo "   ". Colorize::green().implode(" ",$parameter->getItems()) .Colorize::clear(). "\t";
            foreach ($targets as $target) echo ($target[2]?"":"[") . Colorize::blue().Colorize::bold().$target[0].Colorize::clear(). ($target[2]?" ":"] ");
            echo  $parameter->getDescription();
            echo PHP_EOL;
            if (count($targets))
            {
                echo "\t\t".Colorize::underline()."Definição".Colorize::clear() .PHP_EOL;
                foreach ($targets as $target) echo "\t\t-> ".($target[2]?"":"[") . Colorize::blue().$target[0].Colorize::clear(). ($target[2]?"":"]")."\t". $target[1] . ($target[2]?Colorize::red()." *Obrigatorio".Colorize::clear():""). PHP_EOL;
                echo PHP_EOL;
            }

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

    /**
     * @param $parameter
     * @return Parameter
     */
    public function getParameter($parameter) : Parameter
    {
        return $this->values[$parameter];
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getValue($parameter, $target = null)
    {
        $return = '';
        $parameters = is_array($parameter)?$parameter:[$parameter];
        foreach ($parameters as $parameter)
            $return .= isset($this->values[$parameter])?$this->values[$parameter]->getValue($target):null;

        return $return;
    }

    public function run()
    {
        global $argv, $argc;
        unset($argv[0]);

        if (count($argv) > 0)
        {
            if (@$argv[1][0]!='-'){$this->target  = $argv[1]; unset($argv[1]);}
            foreach ($this->parameters as $parameter)
            {
                foreach ($parameter->getItems() as $item)
                {
                    if (in_array($item, $argv))
                    {

                        foreach ($parameter->getTarget() as $i => $target)
                        {
                            //echo "===>{$target[0]} ".$argv[array_search($item, $argv)+1+$i] . PHP_EOL;
                            if ( isset($argv[array_search($item, $argv)+1+$i]) && $argv[array_search($item, $argv)+1+$i][0] != '-' )
                            {
                                //echo "---> ".$argv[array_search($item, $argv)+1+$i].PHP_EOL;
                                $parameter->setValue($target[0], $argv[array_search($item, $argv)+1+$i]);
                                unset($argv[array_search($item, $argv)+1+$i]);
                            }elseif ($target[2] == 'required'){
                                $this->showHelp(" sub parametro ".Colorize::underline(). $target[0]. Colorize::clear(). " do parametro ".Colorize::underline().print_r($item,1).Colorize::clear()  ." é obrigatorio" );
                            }
                        }
                        if (!is_null($parameter->function))$parameter->run();
                        foreach ($parameter->getItems() as $itemTitle)
                            $this->values[$itemTitle] = $parameter;
                        unset($argv[array_search($item, $argv)]);
                    }
                }
            }
        }else{
            echo $this->showHelp();
        }
        if (count($argv)){
            $this->showHelp(" parametro(s) informado nao existem ".Colorize::underline(). implode($argv, ', '). Colorize::clear());
        }

       // echo $this->getLogo();

    }
}
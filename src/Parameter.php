<?php
/**
 * Created by PhpStorm.
 * User: raphaeu
 * Date: 28/02/19
 * Time: 13:39
 */

namespace raphaeu;

class Parameter
{
    private $items = [];
    private $description;
    private $value;
    private $targets;
    public $function;
    private $targetIndex =0 ;

    function __construct($items, $description, $function=null, $targets=[])
    {

        if (!is_array($items)) $items = [$items];
        $this->items = $items;
        $this->description = $description;
        $this->targets = $targets;

        #$this->function = $function?\Closure::fromCallable($function):null;
        if ($function){
            $reflexion = new \ReflectionFunction($function);
            $this->function = $reflexion->getClosure();
        }
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setValue($target, $value)
    {
        $this->value[$target] = $value;
    }

    public function getValue($target=null)
    {
        if (is_null($target))
            return $this->value[$this->getTarget()[0][0]];
        else
            return $this->value[$target];
    }

    public function getTarget()
    {
        return $this->targets;
    }

    public function run()
    {
        call_user_func_array($this->function, []);
    }

}
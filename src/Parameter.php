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
    public $function;

    function __construct($items, $description, $function=null)
    {
        if (!is_array($items)) $items = [$items];
        $this->items = $items;
        $this->description = $description;

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

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function run()
    {
        call_user_func_array($this->function, []);
    }

}
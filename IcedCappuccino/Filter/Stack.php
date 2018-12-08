<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/10/8
 * Time: 1:19
 */

namespace IcedCappuccino\Filter;

class Stack
{
    private $stack = [];
    private $index = -1;

    public function __construct($arr = null)
    {
        if (!empty($arr)){
            $this->index += count($arr);
            $this->stack = $arr;
        }
    }

    public function isEmpty(){
        return !($this->index > -1);
    }

    public function push($item){
        ++$this->index;
        $this->stack[$this->index] = $item;
    }

    public function pushArray($arr){
        $total = count($arr);
        $this->index += $total;
        for($i = 0;$i < $total;$i++){
            array_push($this->stack,$arr[$i]);
        }
    }

    public function pop($del = false){
        try{
            if($this->isEmpty())
                throw new \Exception("the stack is empty!");
        }catch (\Exception $exception){
            exit($exception->getMessage());
        }
        if ($del)
            return $this->stack[$this->index--];
        return $this->stack[$this->index];
    }

    public function getArray(){
        return $this->stack;
    }
}
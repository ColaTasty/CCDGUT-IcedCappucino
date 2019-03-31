<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/10/14
 * Time: 17:04
 */

namespace IcedCappuccino;


abstract class ModuleAbstract
{
    protected $json = [];
    protected $v = "toApp";
    protected $exception = null;
    protected $callBack = null;

    protected function setView($name_view){
        $this->v = $name_view;
    }

    /**
     * @return string
     */
    public function getView(){
        return $this->v;
    }

    protected function setJSON($key,$value){
        $this->json[$key] = $value;
    }

    protected function getJSON(){
        return $this->json;
    }

    protected function setCallBack($msg){
        $this->callBack = $msg;
    }

    protected function getCallBack(){
        return ($this->callBack == null) ? $this->json:$this->callBack;
    }

    protected function setException($msg){
        $this->exception = new \Exception($msg);
        $this->setView("showException");
        $this->setCallBack($this->exception->getMessage());
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/10/8
 * Time: 1:03
 */

namespace IcedCappuccino\Controllor;


use IcedCappuccino\Config;
use IcedCappuccino\ControllorAbstract;

class wxappControllorClass extends ControllorAbstract
{
    public function __construct($module, $method)
    {
        Config::setModules([
            "cet",
            "calendar",
            "wxBusiness",
            "vote",
            "iloveu",
            "msg"
        ]);
        parent::__construct($module, $method);
    }

//    public function run()
//    {
//        try{
//            $str_method = $this->method;
//            @$this->response = $this->m->$str_method();
//        }catch (\Exception $exception){
//            exit("<br>There is error ".$this->method."in ".$this->m."ModuleClass!!!");
//        }
//        $this->toView($this->m->getView());
//    }
}
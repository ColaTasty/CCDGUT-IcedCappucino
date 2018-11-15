<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/10/4
 * Time: 17:42
 */

namespace IcedCappuccino\C;

use IcedCappuccino\Config;
use IcedCappuccino\ControllorAbstract;

class testControllorClass extends ControllorAbstract
{
    public function __construct($module,$method)
    {
        Config::setModules([
            'test',
            'wxBusiness'
        ]);
        parent::__construct($module,$method);
    }

    public function run(){
        try{
            $str_method = $this->method;
            @$this->response = $this->m->$str_method();
        }catch (\Exception $exception){
            exit("<br>There is error ".$this->method."in ".$this->m."ModuleClass!!!");
        }
        $this->toView($this->m->getView());
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/10/4
 * Time: 18:43
 */

namespace IcedCappuccino\M;

//session_id("5mcmcc9gqsb9j0kfvbgc1sc6i0");
//session_start();
use IcedCappuccino\C\Router;
use IcedCappuccino\Config;
use IcedCappuccino\DB;
use IcedCappuccino\Filter\Stack;
use IcedCappuccino\ModuleAbstract;

class testModuleClass extends ModuleAbstract
{
    public function __construct()
    {
    }

    public function testMethod(){
        $this->setView("toApp");
        return "<br>testMethod is invoke!";
    }

    public function wxapp(){
        $this->setView("toApp");
        return "wxapp() is invoke!";
    }

    public function testMark(){
//        var_dump($_SESSION);
    }

    public function dd(){
        $this->setView("test");
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/10/4
 * Time: 18:43
 */

namespace IcedCappuccino\Module;

if (isset($_GET['ssid'])){
    session_id($_GET['ssid']);
    session_start();
}
use IcedCappuccino\Controllor\Router;
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

    public function test(){
        $this->setView("test");
    }

    public function openid(){
        var_dump($_SESSION);
    }

    public function school(){
        header("Location:http://10.20.208.11");
    }
}
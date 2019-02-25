<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/12/9
 * Time: 0:27
 */

namespace IcedCappuccino\Module;


use IcedCappuccino\ModuleAbstract;

class libModuleClass extends ModuleAbstract
{
    public function css(){
        if (!isset($_GET["css"])){
            return;
        }
        header("content-type:text/css");
        $this->setView($_GET['css']);
    }

    public function js(){
        if (!isset($_GET["js"])){
            return;
        }
        header("content-type:text/javascript");
        $this->setView($_GET['js']);
    }
}
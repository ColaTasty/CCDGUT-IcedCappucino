<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/11/2
 * Time: 15:20
 */

namespace IcedCappuccino\M;

session_start();

use IcedCappuccino\ModuleAbstract;

class calendarModuleClass extends ModuleAbstract
{
    public function hello(){
        $this->setJSON("isOK",true);
        $this->setJSON("msg","hello world");
        return $this->getCallBack();
    }

    public function setting(){

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2019/3/28
 * Time: 0:18
 */

namespace IcedCappuccino\Module;


use IcedCappuccino\ModuleAbstract;

class voteModuleClass extends ModuleAbstract
{
    public function __construct()
    {
    }

    public function test(){
        $this->setJSON("isOK",true);
        return $this->getCallBack();
    }
}
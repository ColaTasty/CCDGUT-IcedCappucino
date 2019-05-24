<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2019/5/20
 * Time: 22:16
 */

namespace IcedCappuccino\Module;


use IcedCappuccino\ModuleAbstract;

class msgModuleClass extends ModuleAbstract
{
    public function please_update_wxapp(){
        $this->setView("pleaseUpdateWxapp");
        return $this->getCallBack();
    }
}
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

    public function wxqun_xin_shen(){
        $this->setView("wxqunXinShen");
        return $this->getCallBack();
    }

    public function tieba_new_name(){
        $this->setView("viewNewTiebaName");
        return $this->getCallBack();
    }

    public function test(){
        $this->setJSON("msg","什么鬼？");
        return $this->getCallBack();
    }
}
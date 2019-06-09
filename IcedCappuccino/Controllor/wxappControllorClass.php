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
}
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
    public function css()
    {
        if (!isset($_GET["css"])) {
            return;
        }
        header("content-type:text/css");
        $this->setView($_GET['css']);
    }

    public function js()
    {
        if (!isset($_GET["js"])) {
            return;
        }
        header("content-type:text/javascript");
        $this->setView($_GET['js']);
    }

    public function jpg()
    {
        if (!isset($_GET["jpg"])) {
            return;
        }
        header("content-type:image/jpg");
        $this->setView($_GET['jpg']);
    }

    public function ico()
    {
        if (!isset($_GET["ico"])) {
            return;
        }
        header("content-type:image/ico");
        $this->setView($_GET['ico']);
    }

    public function png()
    {
        if (!isset($_GET["png"])) {
            return;
        }
        header("content-type:image/png");
        $this->setView($_GET['png']);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/12/9
 * Time: 0:24
 */

namespace IcedCappuccino\Controllor;


use IcedCappuccino\Config;
use IcedCappuccino\ControllorAbstract;

class libControllorClass extends ControllorAbstract
{
    public function __construct($module, $method)
    {
        Config::setModules([
            "lib"
        ]);
        parent::__construct($module, $method);
    }

    public function toView($view_name)
    {
        try {
            if (file_exists(__DIR__ . "/../View/css/$view_name.css")) {
                include_once __DIR__ . "/../View/css/$view_name.css";
                return;
            }
            if (file_exists(__DIR__ . "/../View/js/$view_name.js")) {
                include_once __DIR__ . "/../View/js/$view_name.js";
                return;
            }
            if (file_exists(__DIR__ . "/../View/image/$view_name.png")) {
                include_once __DIR__ . "/../View/image/$view_name.png";
                return;
            }
            if (file_exists(__DIR__ . "/../View/image/$view_name.ico")) {
                include_once __DIR__ . "/../View/image/$view_name.ico";
                return;
            }

            if (file_exists(__DIR__ . "/../View/image/$view_name.jpg")) {
                include_once __DIR__ . "/../View/image/$view_name.jpg";
                return;
            }
            throw new \Exception("Page $view_name is not found!!!", 404);
        } catch (\Exception $exception) {
            header("status:" . $exception->getCode());
            header("content-type:text/html");
            include_once __DIR__ . "/../View/404.php";
        }
    }
}
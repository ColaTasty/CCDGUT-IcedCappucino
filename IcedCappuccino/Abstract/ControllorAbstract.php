<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/10/5
 * Time: 15:52
 */

namespace IcedCappuccino;


abstract class ControllorAbstract
{
    protected $m = "";
    protected $v = "";
    protected $method = "";
    protected $modules_list = [];
    protected $response = "";

//    abstract public function setModules($modules);
    abstract public function run();

    public function __construct($module,$method){
        try{
            if (Config::isModules($module)){
                $str_module = "IcedCappuccino\M\\".$module."ModuleClass";
                require_once __DIR__."/../Module/".$module."ModuleClass.php";
                $this->m = new $str_module();
                $this->method = $method;
            }else{
                throw new \Exception("<br>There is no {$module}ModuleClass in this Controllor!!");
            }
        }catch (\Exception $exception){
            exit($exception->getMessage());
        }
    }

    public function toView($view_name)
    {
        try{
            if (!file_exists(__DIR__."/../View/$view_name.php"))
                throw new \Exception("$view_name.php is not found!!!",404);
            include_once __DIR__."/../View/$view_name.php";
        }catch (\Exception $exception){
            header("status:404");
            include_once __DIR__ . "/../View/404.php";
        }
    }
}
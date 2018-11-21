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
            if (file_exists(__DIR__."/../View/$view_name.php"))
                include_once __DIR__."/../View/$view_name.php";
            elseif (file_exists(__DIR__."/../View/$view_name.html"))
                include_once __DIR__."/../View/$view_name.html";
            else{
                throw new \Exception("Page $view_name is not found!!!",404);
            }
        } catch (\Exception $exception){
            header("status:404");
            include_once __DIR__ . "/../View/404.php";
        }
    }
}
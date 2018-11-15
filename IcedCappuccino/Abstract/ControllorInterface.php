<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/10/5
 * Time: 16:04
 */

namespace IcedCappuccino;


interface ControllorInterface
{
    public function __construct($module,$method);
    public function run();
    public function toView($view_name);
}
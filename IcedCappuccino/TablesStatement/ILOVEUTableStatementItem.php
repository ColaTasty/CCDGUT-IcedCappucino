<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2019/5/16
 * Time: 21:55
 */

namespace IcedCappuccino\TablesStatement;


use IcedCappuccino\TableStatementAbstract;

class ILOVEUMessagesStatementItem extends TableStatementAbstract
{
    public function __construct()
    {
        parent::__construct("ILOVEUMessages", ["mid","openid","content","time"]);
    }
}
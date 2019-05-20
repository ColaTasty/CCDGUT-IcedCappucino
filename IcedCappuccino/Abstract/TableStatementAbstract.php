<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2019/5/16
 * Time: 22:05
 */

namespace IcedCappuccino;


abstract class TableStatementAbstract
{
    public $table_name = "";
    public $table_columns = [];
    public $sql = "";
    public $sql_stack = [];
    /**
     * @var null|\PDOStatement
     */
    protected $obj_stat = null;

    /**
     * TableStatementAbstract constructor.
     * @param $table_name
     * @param $table_columns
     */
    public function __construct($table_name, $table_columns)
    {
        $this->table_name = $table_name;
        $this->table_columns = $table_columns;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * @return array
     */
    public function getTableColumns()
    {
        return $this->table_columns;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @return array
     */
    public function getSqlStack()
    {
        return $this->sql_stack;
    }

    /**
     * @return null|\PDOStatement
     */
    public function getObjStat()
    {
        return $this->obj_stat;
    }

    /**
     * @param string|array $columns
     * @param string $where
     * @param string $order
     * @param string $group
     * @param array $sql_param
     * @return null|\PDOStatement|bool
     */
    public function select($columns = "*", $where = "", $order = "", $group = "", $sql_param = [])
    {
        try {
            array_push($this->sql_stack, "SELECT");
            if (is_array($columns)) {
                if (count($tmp = array_diff($this->table_columns, $columns)) > 0)
                    new \Exception("These columns are not in this table : " . implode(", ", $tmp));
                array_push($this->sql_stack, implode(" ", $columns));
            } else {
                array_push($this->sql_stack, $columns);
            }
            array_push($this->sql_stack, "FROM " . $this->table_name);
            if (strlen($where) > 0) {
                array_push($this->sql_stack, "WHERE " . $where);
            }
            if (strlen($order) > 0) {
                array_push($this->sql_stack, "ORDER BY " . $order);
            }
            if (strlen($group) > 0) {
                array_push($this->sql_stack, "GROUP BY " . $group);
            }
            return $this->execute($sql_param);
        } catch (\Exception $ex) {
            exit($ex->getMessage());
        }
    }

    /**
     * @param $columns
     * @param $values
     */
    public function insert($columns, $values)
    {

    }

    /**
     * @param $sql_param
     * @return null|\PDOStatement
     */
    private function execute($sql_param)
    {
        $this->sql = implode(" ", $this->sql_stack);
        $this->obj_stat = DB::executeSQL($this->sql, $sql_param);
        return $this->obj_stat;
    }

}
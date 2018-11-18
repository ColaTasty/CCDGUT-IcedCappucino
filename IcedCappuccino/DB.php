<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2018/11/1
 * Time: 18:10
 */

namespace IcedCappuccino;


class DB
{
    private static $user = "";
    private static $password = "";
    private static $type = "";
    private static $host = "";
    private static $name = "";
    private static $charset = "";
    /** @var \PDO  */
    private static $pdo = "";

    /**
     *@param array
     *@return void
     */
    public static function set($info){
        self::$user = $info['user'];
        self::$password = $info['password'];
        self::$type = $info['type'];
        self::$host = $info['host'];
        self::$charset = isset($info['charset']) ? $info['charset'] : "utf8";
        self::$name = $info['name'];
    }

    /**
     *@param null
     *@return void
     */
    public static function connect(){
        try{
            $dsn = self::$type.":host=".self::$host.";dbname=".self::$name.";charset=".self::$charset;
            self::$pdo = new \PDO($dsn,self::$user,self::$password);
        }catch (\Exception $exception){
            exit($exception->getMessage());
        }
    }

    /**
     *@param string
     *@param array
     *@return \PDOStatement
     */
    public static function executeSQL($sql,$where=[]){
        try{
//            $dsn = self::$type.":host=".self::$host.";dbname=".self::$name.";charset=".self::$charset;
//            self::$pdo = new \PDO($dsn,self::$user,self::$password);
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($where);
            if(!$stmt){
                $err = [
                    "isOK" => false,
                    "msg" => "Error Executed SQL",
                    $stmt->errorInfo()
                ];
                throw new \Exception(json_encode($err));
            }
        }catch (\Exception $exception){
            exit($exception->getMessage());
        }
        return $stmt;
    }
}
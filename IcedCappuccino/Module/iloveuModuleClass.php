<?php
/**
 * Created by PhpStorm.
 * User: Makia
 * Date: 2019/5/16
 * Time: 21:29
 */

/**
 * SESSION = [
 * "sessionKey",
 * "expireTime",
 * "openId",
 * "uid"]
 */

namespace IcedCappuccino\Module;

include __DIR__ . "/../ModuleConfig/iloveuConfig.php";

use IcedCappuccino\DB;
use IcedCappuccino\ModuleAbstract;
use IcedCappuccino\ModuleConfig\iloveuConfig;
use function MongoDB\BSON\toJSON;

$ssid = null;
if (isset($_POST["ssid"])) {
    $ssid = $_POST["ssid"];
    session_id($ssid);
    session_start();
}
DB::connect();

class iloveuModuleClass extends ModuleAbstract implements iloveuConfig
{
    public function hello()
    {
        $this->setJSON("isOK", true);
        $this->setJSON("msg", "hello world");
        return $this->getCallBack();
    }

    public function init()
    {
        $this->setJSON("canUse", self::canUse);
        $this->setJSON("inform", self::inform);
        return $this->getCallBack();
    }

    public function sending()
    {
        if (!$_SESSION) {
            $this->setJSON("isOK", false);
            $this->setJSON("msg", "用户身份过期，请重新加载小程序");
            return $this->getCallBack();
        }
        $param = [
            ":openid" => $_SESSION["openId"],
            ":content" => json_encode([
                "content" => $_POST["content"]
            ]),
            ":time" => time()
        ];
        $sql = "INSERT INTO ILOVEUMessages(openid,content,time) VALUES(:openid,:content,:time)";
        $stat = DB::executeSQL($sql, $param);
        $rowCount = $stat->rowCount();
        $this->setJSON("isOK", $rowCount > 0);
        if (!$this->getJSON()["isOK"]) {
            $this->setJSON("msg", "发送失败，请重试");
            $this->setJSON("error", $stat->errorInfo());
        } else
            $this->setJSON("mid", DB::getPDO()->lastInsertId());
        return $this->getCallBack();
    }

    public function show()
    {
        if (!$_SESSION) {
            $this->setJSON("isOK", false);
            $this->setJSON("msg", "用户身份过期，请重新加载小程序");
            return $this->getCallBack();
        }
        if (isset($_REQUEST['mid'])) {
//            获取指定的表白
            return $this->getSpecificMessage($_REQUEST["mid"]);
        } else {
            if (isset($_REQUEST["myself"])) {
//                获取我的表白
                return $this->getMyMessages($_SESSION["openId"]);
            } else {
//                获取主页列表
                return $this->getMessages($_REQUEST["hasCount"]);
            }
        }
    }

    private function getSpecificMessage($mid)
    {
        $sql = "SELECT mid,`content`,`time`,`like`,`share` FROM ILOVEUMessages WHERE mid=:mid AND status=1";
        $params = [
            ":mid" => $mid
        ];
        $stat = DB::executeSQL($sql, $params);
        if ($stat->rowCount() <= 0) {
            $this->setJSON("isOK", false);
            $this->setJSON("msg", "查找失败，可能这条表白已经被删除咯~");
            $this->setJSON("errorMsg", $params);
            return $this->getCallBack();
        }
        foreach ($stat as $row) {
            $content = json_decode($row["content"]);
            $message["mid"] = $row["mid"];
            $message["content"] = $content->content;
            $message["time"] = $row["time"];
            $message["like"] = $row["like"];
            $message["share"] = $row["share"];
        }
        $sql = "SELECT lid FROM ILOVEULikes WHERE openid=:openid AND mid=:mid";
        $params[":openid"] = $_SESSION["openId"];
        $stat = DB::executeSQL($sql, $params);
        $message["is_like"] = $stat->rowCount() > 0;
        $sql = "SELECT sid FROM ILOVEUShare WHERE openid=:openid AND mid=:mid";
        $stat = DB::executeSQL($sql, $params);
        $message["is_share"] = $stat->rowCount() > 0;
        $this->setJSON("isOK", true);
        $this->setJSON("message", $message);
        return $this->getCallBack();
    }

    private function getMyMessages($openid)
    {
        $hasCount = $_REQUEST["hasCount"];
        $start = $hasCount > 0 ? $hasCount + 1 : $hasCount;
        $need = 10;
        $sql = "SELECT mid FROM ILOVEUMessages WHERE openid=:openid AND status=1 ORDER BY mid DESC LIMIT {$start},{$need}";
        $params = [
            ":openid" => $openid
        ];
        $stat = DB::executeSQL($sql, $params);
        if ($stat->rowCount() <= 0) {
            $sql = "SELECT COUNT(mid) AS total FROM ILOVEUMessages WHERE openid=:openid AND status=1";
            $stat = DB::executeSQL($sql, $params);
            foreach ($stat as $row) {
                $this->setJSON("isEnd", $hasCount + 10 > $row["total"]);
            }
            $this->setJSON("isOK", false || $this->getJSON()["isEnd"]);
            $this->setJSON("msg", "你还没有表白过噢~");
            $this->setJSON("errorMsg", $params);
            return $this->getCallBack();
        }
        $i = 0;
        $messages[$i] = 0;
        foreach ($stat as $row) {
            $tmp = $this->getSpecificMessage($row["mid"]);
            $messages[$i] = $tmp["message"];
            $i++;
        }
        $sql = "SELECT COUNT(mid) AS total FROM ILOVEUMessages WHERE openid=:openid AND status=1";
        $stat = DB::executeSQL($sql, $params);
        foreach ($stat as $row) {
            $this->setJSON("isEnd", $hasCount + 10 > $row["total"]);
        }
        $this->setJSON("isOK", true);
        $this->setJSON("messages", $messages);
        unset($this->getJSON()["message"]);
        return $this->getCallBack();
    }

    private function getMessages($hasCount)
    {
        $messages_idx = 0;
        $messages = [];
        if ($hasCount == 0) {
//        获取热门告白
            $this->getHotMessages($messages, $messages_idx);
//        随机推荐告白
            $this->getRandMessages($messages, $messages_idx);
        }
//        普通获取告白
        $start = $hasCount - 5 > 0 ? $hasCount - 5 : 0;
        $need = 10;
        $sql = "SELECT mid FROM ILOVEUMessages WHERE status=1 ORDER BY mid DESC LIMIT {$start},{$need}";
        $stat = DB::executeSQL($sql);
        foreach ($stat as $row) {
            $message = $this->getSpecificMessage($row["mid"])["message"];
            $message["flag"] = 2;
            $messages[$messages_idx] = $message;
            $messages_idx++;
        }
        $sql = "SELECT COUNT(mid) AS total FROM ILOVEUMessages WHERE status=1";
        $stat = DB::executeSQL($sql);
        $rowCount = $stat->rowCount();
        if ($rowCount <= 0) {
            $this->setJSON("isOK", false);
            $this->setJSON("msg", "还没有人表白噢，快写下表白墙的第一个表白吧！");
            return $this->getCallBack();
        }
        foreach ($stat as $row) {
            $this->setJSON("isEnd", $hasCount + 10 > $row["total"]);
        }
        $this->setJSON("isOK", true);
        $this->setJSON("messages", $messages);
        unset($this->getJSON()["message"]);
        return $this->getCallBack();
    }

    private function getHotMessages(&$messages, &$messages_idx)
    {
        $sql = "SELECT mid FROM ILOVEUMessages WHERE (`like`>30 OR `share` >30) AND status=1 ORDER BY mid DESC LIMIT 1000";
        $stat = DB::executeSQL($sql);
        $rowCount = $stat->rowCount();
        if ($rowCount <= 0) {
            return 0;
        }
        $mids = [];
        if ($rowCount > 0) {
            foreach ($stat as $row) {
                array_push($mids, $row["mid"]);
            }
            $hot_mids_idx = [];
            for ($i = 0; $i < ($rowCount >= 3 ? 3 : $rowCount); $i++) {
                $t = rand(0, count($mids) - 1);
                if (in_array($t, $hot_mids_idx)) {
                    $i--;
                    continue;
                }
                array_push($hot_mids_idx, $t);
            }
            foreach ($hot_mids_idx as $idx) {
                $tmp = $this->getSpecificMessage($mids[$idx]);
                $message = $tmp["message"];
                $message["flag"] = 0;
                $messages[$messages_idx] = $message;
                $messages_idx++;
            }
        }
        return true;
    }

    private function getRandMessages(&$messages, &$messages_idx)
    {
        $sql = "SELECT mid FROM ILOVEUMessages WHERE status=1 ORDER BY mid DESC";
        $stat = DB::executeSQL($sql);
        $rowCount = $stat->rowCount();
        $mids = [];
        if ($rowCount > 10) {
            foreach ($stat as $row) {
                array_push($mids, $row["mid"]);
            }
            $rand_mids_idx = [];
            for ($i = 0; $i < 2; $i++) {
                $t = rand(0, count($mids) - 1);
                if (in_array($t, $rand_mids_idx)) {
                    $i--;
                    continue;
                }
                array_push($rand_mids_idx, $t);
            }
            foreach ($rand_mids_idx as $idx) {
                $tmp = $this->getSpecificMessage($mids[$idx]);
                $message = $tmp["message"];
                $message["flag"] = 1;
                $message["errorMsg"] = $mids[$idx];
                $messages[$messages_idx] = $message;
                $messages_idx++;
            }
        }
        return true;
    }

    public function like()
    {
        if (!$_SESSION) {
            $this->setJSON("isOK", false);
            $this->setJSON("msg", "用户身份过期，请重新加载小程序");
            return $this->getCallBack();
        }
        $sql = "INSERT INTO ILOVEULikes(mid,openid,time,identy_key) VALUES(:mid,:openid,:time,:identy_key)";
        $params = [
            ":mid" => $_REQUEST["mid"],
            ":openid" => $_SESSION["openId"],
            ":time" => time(),
            ":identy_key" => $_SESSION["openId"] . $_REQUEST["mid"]
        ];
        $stat = DB::executeSQL($sql, $params);
        $rowCount = $stat->rowCount();
        $this->setJSON("isOK", $rowCount > 0);
        if (!$this->getJSON()["isOK"]) {
            $this->setJSON("msg", "点赞失败，请重试");
            $this->setJSON("error", $stat->errorInfo());
        }

        $total = $this->countLike($params[":mid"]);
        $sql = "UPDATE ILOVEUMessages SET `like` = {$total} WHERE mid=:mid";
        $stat = DB::executeSQL($sql, [":mid" => $_REQUEST["mid"]]);
        $rowCount = $stat->rowCount();
        $this->setJSON("isOK", $rowCount > 0);
        if (!$this->getJSON()["isOK"]) {
            $this->setJSON("msg", "点赞数添加失败，请重试");
            $this->setJSON("error", $stat->errorInfo());
        }
        return $this->getSpecificMessage($_REQUEST["mid"]);
    }

    public function unlike()
    {
        if (!$_SESSION) {
            $this->setJSON("isOK", false);
            $this->setJSON("msg", "用户身份过期，请重新加载小程序");
            return $this->getCallBack();
        }
        $sql = "DELETE FROM ILOVEULikes WHERE mid=:mid AND openid=:openid";
        $params = [
            ":mid" => $_REQUEST["mid"],
            ":openid" => $_SESSION["openId"]
        ];
        $stat = DB::executeSQL($sql, $params);
        $rowCount = $stat->rowCount();
        $this->setJSON("isOK", $rowCount > 0);
        if (!$this->getJSON()["isOK"]) {
            $this->setJSON("msg", "取消点赞失败，请重试");
            $this->setJSON("error", $stat->errorInfo());
        }

        $total = $this->countLike($params[":mid"]);
        $sql = "UPDATE ILOVEUMessages SET `like` = {$total} WHERE mid=:mid";
        $stat = DB::executeSQL($sql, [":mid" => $_REQUEST["mid"]]);
        $rowCount = $stat->rowCount();
        $this->setJSON("isOK", $rowCount > 0);
        if (!$this->getJSON()["isOK"]) {
            $this->setJSON("msg", "点赞数剔除失败，请重试");
            $this->setJSON("error", $stat->errorInfo());
        }
        return $this->getSpecificMessage($_REQUEST["mid"]);
    }

    private function countLike($mid)
    {
        $sql = "SELECT COUNT(lid) AS total FROM ILOVEULikes WHERE mid=:mid";
        $params = [
            ":mid" => $mid
        ];
        $stat = DB::executeSQL($sql, $params);
        $total = 0;
        if ($stat->rowCount() <= 0) {
            return $total;
        }
        foreach ($stat as $row) {
            $total = $row["total"];
        }
        return $total;
    }

    public function share()
    {
        if (!$_SESSION) {
            $this->setJSON("isOK", false);
            $this->setJSON("msg", "用户身份过期，请重新加载小程序");
            return $this->getCallBack();
        }
        $sql = "SELECT sid FROM ILOVEUShare WHERE mid=:mid AND openid=:openid";
        $params = [
            ":mid" => $_REQUEST["mid"],
            ":openid" => $_SESSION["openId"],
        ];
        $stat = DB::executeSQL($sql, $params);
        if ($stat->rowCount() > 0) {
            $this->setJSON("isOK", true);
            $this->setJSON("msg", "用户已经分享过了");
            return $this->getCallBack();
        }
        $sql = "INSERT INTO ILOVEUShare(mid,openid,time) VALUES (:mid,:openid,:time)";
        $params = [
            ":mid" => $_REQUEST["mid"],
            ":openid" => $_SESSION["openId"],
            ":time" => time()
        ];
        $stat = DB::executeSQL($sql, $params);
        $rowCount = $stat->rowCount();
        $this->setJSON("isOK", $rowCount > 0);
        if (!$this->getJSON()["isOK"]) {
            $this->setJSON("msg", "分享失败，请重试");
            $this->setJSON("error", $stat->errorInfo());
        }

        $sql = "UPDATE ILOVEUMessages SET `share` = `share`+1 WHERE mid=:mid";
        $stat = DB::executeSQL($sql, [":mid" => $_REQUEST["mid"]]);
        $rowCount = $stat->rowCount();
        $this->setJSON("isOK", $rowCount > 0);
        if (!$this->getJSON()["isOK"]) {
            $this->setJSON("msg", "分享添加失败，请重试");
            $this->setJSON("error", $stat->errorInfo());
        }
        return $this->getSpecificMessage($_REQUEST["mid"]);
    }

    public function delete()
    {
        if (!$_SESSION) {
            $this->setJSON("isOK", false);
            $this->setJSON("msg", "用户身份过期，请重新加载小程序");
            return $this->getCallBack();
        }
        return $this->alter_status(0);
    }

    private function alter_status($status)
    {
        $sql = "UPDATE ILOVEUMessages SET status=:status WHERE mid=:mid";
        $params = [
            ":status" => $status,
            ":mid" => $_REQUEST["mid"]
        ];
        $stat = DB::executeSQL($sql, $params);
        $rowCount = $stat->rowCount();
        if ($rowCount <= 0) {
            $this->setJSON("msg", "删除失败，未知原因");
            $this->setJSON("errorMsg", $stat->errorInfo());

        }
        $this->setJSON("isOK", $rowCount > 0);
        return $this->getCallBack();
    }
}
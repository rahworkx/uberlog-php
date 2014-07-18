<?php
namespace Logger;

include_once(__DIR__."/resp_client.php");
class Client
{

    static $connection = null;
    static $namespace = null;
    static $socket_address = "unix:///tmp/redis2.sock";

    public static function getConnection()
    {
        if (self::$connection === null) {
            self::$connection = new RespClient(self::$socket_address, -1);
        }
        return self::$connection;
    }

    protected static function log($level, $category, $slug, $extra_params)
    {
        $c = self::getConnection();
        $extra_params = (array) $extra_params;
        if (self::$namespace !== null) {
            $extra_params["__namespace"] = self::$namespace;
        }
        return $c->log($level, $category, $slug, json_encode($extra_params));
    }

    public static function success($category, $slug, $extra_params)
    {
        $c = self::getConnection();
        return $c->log("success", $category, $slug, json_encode($extra_params));
    }

    public static function info($category, $slug, $extra_params)
    {
        $c = self::getConnection();
        return $c->log("info", $category, $slug, json_encode($extra_params));
    }

    public static function warning($category, $slug, $extra_params)
    {
        $c = self::getConnection();
        return $c->log("warning", $category, $slug, json_encode($extra_params));
    }

    public static function error($category, $slug, $extra_params)
    {
        $c = self::getConnection();
        return $c->log("error", $category, $slug, json_encode($extra_params));
    }

    public static function debug($category, $slug, $extra_params)
    {
        $c = self::getConnection();
        return $c->log("debug", $category, $slug, json_encode($extra_params));
    }
}

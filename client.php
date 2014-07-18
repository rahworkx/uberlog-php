<?php
namespace Logger;

include_once(__DIR__."/resp_client.php");
class Client
{

    static $connection = null;
    static $namespace = null;
    static $socket_address = "unix:///tmp/logger-proxy.sock";

    public static function getConnection()
    {
        if (self::$connection === null) {
            try {
                self::$connection = new RespClient(self::$socket_address, -1);
            } catch (\Exception $e) {
                self::$connection = null;
                return false;
            }

        }
        return self::$connection;
    }

    protected static function log($level, $category, $slug, $extra_params)
    {
        $c = self::getConnection();
        if ($c === false) {
            return false;
        }
        $extra_params = (array) $extra_params;
        if (self::$namespace !== null) {
            $extra_params["__namespace"] = self::$namespace;
        }
        try {
            $return = $c->log($level, $category, $slug, json_encode($extra_params));
        } catch (\Exception $e) {
            self::$connection = null;
            return false;
        }
        return $return;
    }

    public static function success($category, $slug, $extra_params)
    {
        return self::log("success", $category, $slug, $extra_params);
    }

    public static function info($category, $slug, $extra_params)
    {
        return self::log("info", $category, $slug, $extra_params);
    }

    public static function warning($category, $slug, $extra_params)
    {
        return self::log("warning", $category, $slug, $extra_params);
    }

    public static function error($category, $slug, $extra_params)
    {
        return self::log("error", $category, $slug, $extra_params);
    }

    public static function debug($category, $slug, $extra_params)
    {
        return self::log("debug", $category, $slug, $extra_params);
    }
}

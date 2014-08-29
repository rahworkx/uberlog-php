<?php
namespace UberLog;

include_once(__DIR__."/http_client.php");

class Log
{

    static $http_clients = null;
    static $namespace = null;
    static $api_end_point = "http://127.0.0.1:9876";

    public static function getConnection($level, $category, $slug)
    {
        $key = $level.$category.$slug;
        if (self::$http_clients[$key] === null) {
            try {
                self::$http_clients[$key] = new HttpClient(self::$api_end_point.'/log/'.$level.'/'.$category.'/'.$slug.'/');
            } catch (\Exception $e) {
                self::$http_clients[$key] = null;
                return false;
            }

        }
        return self::$http_clients[$key];
    }

    protected static function log($level, $category, $slug, $extra_params = array())
    {
        $c = self::getConnection($level, $category, $slug);
        if ($c === false) {
            return false;
        }
        $extra_params = (array) $extra_params;
        if (self::$namespace !== null) {
            $extra_params["__namespace"] = self::$namespace;
        }
        if(is_array($extra_params) && empty($extra_params)){
            $extra_params = new \StdClass();
        }
        try {

            $return = $c->postArray($extra_params);
        } catch (\Exception $e) {
            return false;
        }
        return $return;
    }

    public static function success($category, $slug, $extra_params = array())
    {
        return self::log("success", $category, $slug, $extra_params);
    }

    public static function info($category, $slug, $extra_params = array())
    {
        return self::log("info", $category, $slug, $extra_params);
    }

    public static function warning($category, $slug, $extra_params = array())
    {
        return self::log("warning", $category, $slug, $extra_params);
    }

    public static function error($category, $slug, $extra_params = array())
    {
        return self::log("error", $category, $slug, $extra_params);
    }

    public static function debug($category, $slug, $extra_params = array())
    {
        return self::log("debug", $category, $slug, $extra_params);
    }
}

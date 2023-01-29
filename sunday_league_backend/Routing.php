<?php

require __DIR__ . '/src/controllers/sundayLeague.php';


class Router
{
    public static $routes;

    public static function get($url, $handler)
    {
        self::$routes[$url] = $handler;
    }

    public static function post($url, $handler)
    {
        self::$routes[$url] = $handler;
    }

    public static function run($url)
    {
        $action = explode("/", $url)[0];
        if (!array_key_exists($action, self::$routes)) {
            die("Wrong url!");
        }

        $controller = self::$routes[$action];

        $object = new $controller;
        $action = $action ?: 'index';

        try {
            $object->$action();
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}

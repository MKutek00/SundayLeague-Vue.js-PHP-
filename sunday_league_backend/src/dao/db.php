<?php

// namespace PDO;

class PDOdb
{

    protected static $instance;

    private static $dsn = 'mysql:host=host.docker.internal;dbname=sunday_league';

    private static $username = 'root';

    private static $password = 'admin';

    private function __construct()
    {
        try {
            self::$instance = new \PDO(self::$dsn, self::$username, self::$password);
            self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC); //make the default fetch be an associative array
        } catch (\PDOException $e) {
            echo "MySql Connection Error: " . $e->getMessage();
        }
    }

    public static function get()
    {
        if (!self::$instance) {
            new PDOdb();
        }

        return self::$instance;
    }
}

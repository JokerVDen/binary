<?php


namespace App\DB;


use PDO;

class DB
{
    /**
     * @var PDO
     */
    private static ?PDO $db = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return PDO|null
     */
    public static function getDb(): ?PDO
    {
        if (!self::$db) {
            self::$db = new PDO('mysql:host=mariadb;dbname=binary', 'user', '123456'); //Это бы я вынес в .env
        }
        
        return self::$db;
    }
}
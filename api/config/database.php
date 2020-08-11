<?php

include __DIR__ ."/../objects/SystemInfo.php";
class Database
{

    private static $database = null;
    private $connection;
    private $host;
    private $db_name;
    private $username;
    private $password;

    // getting database credentials from json file
    private function __construct() {
        $this->host     = SystemInfo::$host;
        $this->db_name  = SystemInfo::$db_name;
        $this->username = SystemInfo::$username;
        $this->password = SystemInfo::$password;
        try {
            $this->connection = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->connection->exec("set names utf8");
        }
        catch(PDOException $exception) {
            die (json_encode(
                array(
                    "error" => "Connection error",
                    "message" => $exception->getMessage()
                )
            ));
        }
    }

    public static function getInstance() {
        if(self::$database == null) {
            return self::$database = new Database();
        } 
        return self::$database;
    }

    public function getConnection() {
        return $this->connection;
    }
}
?>

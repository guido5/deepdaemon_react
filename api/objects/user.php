<?php

class User{

    private $conn;
    private $id;
    private $name;
    private $lastname;
    private $username;
    private $password;
    private $permissions;


    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function validate($username, $password){
        $query = "SELECT * FROM access where username='$username' and password='$password'";
         // prepare query statement
         $stmt = $this->conn->prepare($query);
         // execute query
         $stmt->execute();
         $number = $stmt->rowCount();
         if($number == 1 ) {
             return true;
         }
         
    }

    private function parse($stmt) {
        $num = $stmt->rowCount();
        // check if more than 0 record found
        if ($num = 1) {
            //Algoritmo de parseo
        }
    }
}

?>
<?php

class User implements  JsonSerializable{

    private $id;
    private $name;
    private $lastname;
    private $username;
    private $password;
    private $permissions;


    public function __construct($id, $name, $lastname, $username, $password, $permissions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastname = $lastname;
        $this->username = $username;
        $this->password = $password;
        $this->permissions = $permissions;
    }

    public function getId() { return $this->id; }

    public function getName() { return $this->name; }

    public function getLastname() { return $this->lastname; }

    public function getUsername() { return $this->username; }

    public function getPassword() { return $this->password; }

    public function getPermissions() { return $this->permissions; }

    public function jsonSerialize()
    {
        return 
        [
            'id'   => $this->getId(),
            'name' => $this->getName(),
            'lastname' => $this->getLastname(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'permissions' => $this->getPermissions()
        ];
    }

    public static function validate($username, $password){
        $database = Database::getInstance();
        $conn = $database->getConnection();
        $query = "SELECT * FROM access where username='$username' and password='$password'";
        // prepare query statement
        $stmt = $conn->prepare($query);
        // execute query
        $stmt->execute();
        $user = User::parseUser($stmt);
        return $user;
    }

    private static function parseUser($stmt) {
        $num = $stmt->rowCount();
        if ($num == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return new User(
                $row["id"],
                $row["name"],
                $row["lastname"],
                $row["username"],
                $row["password"],
                $row["permissions"]
            );
        }
    }
}

?>
<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));

// include database and util files
include_once './config/database.php';
include_once './config/util.php';
include_once './objects/SystemInfo.php';
include_once './objects/user.php';


/* Convertir conexion de la base de datos a singleton */

// instantiate database object
$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$username = $_GET['username'];
if ($user->validate($username, $_GET['password'])) {
    echo json_encode( array("access" => true));
} else {
    echo "Alerta, usuario no reconocido.";
}

//header('Location: '.SystemInfo::$urlAdminConection);

?>
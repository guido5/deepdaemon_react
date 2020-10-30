<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));

// include database and util files
include_once '../config/database.php';
include_once '../config/util.php';
include_once '../SystemInfo.php';
include_once '../model/user.php';


isset($_POST['username']) ? $username = $_POST['username'] : $username = null;
isset($_POST['password']) ? $password = $_POST['password'] : $password = null;

$user = User::validate($username, $password);
($user == null) ? $json = json_encode(array("user" => null)) : $json = json_encode(array("user" => $user));
echo $json;

?>
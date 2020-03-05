<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));

// include database and util files
include_once './config/database.php';
include_once './config/util.php';
include_once './objects/member.php';

// instantiate database object
$database = new Database();
$db = $database->getConnection();

// initialize object
$member = new Member($db);

$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method) {
    case 'GET':
        // Retrive members
        if(isset($_GET["member_id"])) {
            $member_id=intval($_GET["member_id"]);
            $member->read($member_id);
        }
        else {
            $status = empty($_GET["status"])?"current":$_GET["status"];
            $member->read_all($status);
        }

        break;
    case 'POST':
        $nombre = $_POST["nombre"];
        $apellido = $_POST["apellido"];
        $linkedin = $_POST["linkedin"];
        $email = $_POST["email"];
        $short_desc = $_POST["short_desc"];
        $long_desc = $_POST["long_desc"];
        $status = $_POST["status"];
        $ss = $_POST["ss"];
        $codeOp = $_POST["codeOp"];

        switch($codeOp) {
            case 'create':
                $member->write($nombre, 
                        $apellido,
                        $linkedin,
                        $email,
                        $short_desc,
                        $long_desc,
                        $status,
                        $ss);
            break;
            case 'update':
                $id = $_POST['id'];
                $member->update($id,
                        $nombre, 
                        $apellido,
                        $linkedin,
                        $email,
                        $short_desc,
                        $long_desc,
                        $status,
                        $ss);
            break;
            case 'delete':
                $id = $_POST['id'];
                $member->delete($id);
            break;
        }
        
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
?>

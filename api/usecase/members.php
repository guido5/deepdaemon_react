<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));

// include database and util files
include_once '../config/database.php';
include_once '../config/util.php';
include_once '../model/member.php';
include_once '../SystemInfo.php';

// instantiate database object
$database = Database::getInstance();

// initialize object
$member = new Member($database);

$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method) {
    case 'GET':
        // Retrive members
        if(isset($_GET["member_id"])) {
            $member_id=intval($_GET["member_id"]);
            $member->read($member_id);
        } else if(isset($_GET["status"])){
            $status = empty($_GET["status"])?"current":$_GET["status"];
            $member->readByStatus($status);
        } else if(!empty($_GET["all"])) {
            $member->read_complete();
        }
         else {
            $member->readAll();
        }

        break;
    case 'POST':
        $name = $_POST["name"];
        $lastname = $_POST["lastname"];
        $linkedin = $_POST["linkedin"];
        $email = $_POST["email"];
        $short_desc = $_POST["short_desc"];
        $long_desc = $_POST["long_desc"];
        $status = $_POST["status"];
        $ss = $_POST["ss"];
        $codeOp = $_POST["codeOp"];

        switch($codeOp) {
            case 'create':
                $member->write($name, 
                        $lastname,
                        $linkedin,
                        $email,
                        $short_desc,
                        $long_desc,
                        $status,
                        $ss);
                        header('Location: '.SystemInfo::$urlAdminConection);
            break;
            case 'update':
                $id = $_POST['id'];
                $member->update($id,
                        $name, 
                        $lastname,
                        $linkedin,
                        $email,
                        $short_desc,
                        $long_desc,
                        $status,
                        $ss);
                        header('Location: '.SystemInfo::$urlAdminConection);
            break;
            case 'delete':
                $id = $_POST['id'];
                $member->deleteFromDatabase($id);
                header('Location: '.SystemInfo::$urlAdminConection);
            break;
        }
        
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
?>
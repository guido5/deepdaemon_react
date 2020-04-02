<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));

// include database and util files
include_once './config/database.php';
include_once './config/util.php';
include_once './objects/project.php';
include_once './objects/SystemInfo.php';

// instantiate database object
$database = new Database();
$db = $database->getConnection();

// initialize object
$project = new Project($db);

$request_method=$_SERVER["REQUEST_METHOD"];
switch($request_method)
{
    case 'GET':
        // Retrive Projects
        if(!empty($_GET["project_id"]))
        {
            $project_id=intval($_GET["project_id"]);
            $project->read($project_id);
        }
        else if(!empty($_GET["state"]))
        {
            $state = $_GET["state"];
            $project->read_by_state($state);
        }
        else 
        {
            $project->read_all();
        }
        break;
    case 'POST':
        $codeOp = $_POST['codeOp'];
        $name = $_POST['name'];
        $desc = $_POST['desc'];
        $state = $_POST['state'];
        $impact = $_POST['impact'];
        $modal_type = $_POST['modal_type'];
        $link = $_POST['link'];
        switch($codeOp) {
            case 'create':
                $project->create($name, $desc, $state, $impact, $modal_type, $link);
                sleep(5);
                header('Location: '.SystemInfo::$urlAdminConection);
                break;
            case 'update': 
                $id = $_POST['id'];
                $project->update($id, $name, $desc, $state, $impact, $modal_type, $link);
                header('Location: '.SystemInfo::$urlAdminConection);
                break;
            case 'delete':
                $id = $_POST['id'];
                $project->deleteFromDatabase($id);
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

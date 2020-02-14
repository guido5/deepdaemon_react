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
        // Insert Product
        //$data = json_decode(file_get_contents("php://input"));
        //insert_product();
        break;
    case 'PUT':
        // Update Product
        //$product_id=intval($_GET["product_id"]);
        //update_product($product_id);
        break;
    case 'DELETE':
        // Delete Product
        //$product_id=intval($_GET["product_id"]);
        //delete_product($product_id);
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
?>

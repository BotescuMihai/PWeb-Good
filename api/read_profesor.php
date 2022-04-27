<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'database.php';
include_once 'profesor.php';
$database = new Database();
$db = $database->getConnection();
$items = new Profesor($db);
$stmt = $items->getEmployees();
$itemCount = $stmt->rowCount();

echo json_encode($itemCount);
if ($itemCount > 0) {

    $profesorArr = array();
    $profesorArr["body"] = array();
    $profesorArr["itemCount"] = $itemCount;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $e = array(
            "id" => $id,
            "username" => $username
        );
        array_push($profesorArr["body"], $e);
    }
    echo json_encode($profesorArr);
} else {
    http_response_code(404);
    echo json_encode(
        array("message" => "No record found.")
    );
}
?>
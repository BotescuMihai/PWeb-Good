<?php
header("Content-Type:application/json");
include('database.php');
$con = mysqli_connect("localhost", "root", "root", "proiectpw");
$result = mysqli_query(
    $con,
    "SELECT * FROM `profesor` ");
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result);
    $id = $row['id'];
    $username = $row['username'];
    response($id, $username);
    mysqli_close($con);

} else {
    response(NULL, NULL, 200, "No Record Found");
}
function response($id, $username)
{
    $response['id'] = $id;
    $response['username'] = $username;

    $json_response = json_encode($response);
    echo $json_response;
}

?>
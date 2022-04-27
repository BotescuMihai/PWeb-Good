<?php

$table = '';
/** header("Content-Type:application/json"); **/ /* the format we're going to use. */
//include('database.php');
$con = mysqli_connect("localhost", "root", "root", "proiectpw");
$result = mysqli_query(
    $con,
    "SELECT * FROM `profesor` ");
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result);
    $id = $row['id'];
    $username = $row['username'];
    $table .= '<tr><th scope="row">Username</th><td>' . $username . '</td></tr>';
    mysqli_close($con);
} else {
    //response(NULL, NULL, 200,"No Record Found");
    $table .= '<tr><th scope="row">Status</th><td>INDISPONIBIL</td></tr>';
}
echo $table;


?>
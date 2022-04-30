<?php
//echo $_POST['email'];
//include 'db.php';
session_start();
$_SESSION['email'] = $_POST['email'];
var_dump($_SESSION['email']);
?>
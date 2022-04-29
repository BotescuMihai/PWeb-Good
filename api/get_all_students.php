<?php
session_start();
include 'grades_classification.php';
include 'db.php';
$DB = new db();

if (!isset($_SESSION['profesor_ID_m'])) {
    echo '<h1>Error!</h1><h3>Not found</h3>';
    die();
}
$query = "SELECT DISTINCT student.id, student.username FROM student INNER JOIN note on note.student_id = student.id INNER JOIN materie on note.materie_id = materie.id WHERE materie.id=" . $_SESSION['profesor_ID_m'];
//unset($_SESSION['profesor_ID_m']);
$stmt = $DB->execute_SELECT($query);
$note = array();
if (count($stmt) == 0) {
    $arr = array('Error' => 'No Records found!');
    echo json_encode($arr);
} else {
    $materie = '';
    $uname = '';
    foreach ($stmt as $row) {
        $studenti[] = array('id' => $row['id'], 'username' => $row['username']);
    }
    echo json_encode($studenti);
}
?>
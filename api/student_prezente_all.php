<?php
session_start();
include 'grades_classification.php';
include 'db.php';
$DB = new db();

if (!isset($_SESSION['student_ID_m'])) {
    echo '<h1>Error!</h1><h3>Not found</h3>';
    die();
}
$query = "SELECT materie.denumire, student.id AS sid, materie.id AS mid, student.username, prezenta.tip_prezenta, prezenta.numar_prezente
FROM prezenta
INNER JOIN student ON prezenta.student_id = student.id
INNER JOIN materie ON prezenta.materie_id = materie.id
WHERE prezenta.materie_id = " . $_SESSION['student_ID_m'];
$stmt = $DB->execute_SELECT($query);
$note = array();
if (count($stmt) == 0) {
    $arr = array('Error' => 'No Records found!');
    echo json_encode($arr);
} else {
    foreach ($stmt as $row) {
        $prezente[] = array('username' => $row['username'], 'materie' => $row['denumire'], 'student_id' => $row['sid'], 'materie_id' => $row['mid'], 'tip' => $row['tip_prezenta'], 'numar' => $row['numar_prezente']);
    }
    echo json_encode($prezente);
}
?>
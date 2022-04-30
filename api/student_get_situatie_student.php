<?php
session_start();
include 'grades_classification.php';
include 'db.php';
$DB = new db();
if (!isset($_SESSION['ID_m']) {
    echo '<h1>Error!</h1><h3>Not found</h3>';
    die();
}
$query = "SELECT student.username AS un, tip_nota, pondere, valoare, materie.denumire, profesor.username FROM note INNER JOIN materie ON materie.id = note.materie_id INNER JOIN profesor on materie.profesor_id = profesor.id INNER JOIN student ON student.id = note.student_id WHERE student.username='" . "'" .  . " AND note.materie_id =" . $_SESSION['profesor_ID_m'];
unset($_SESSION['profesor_ID_m']);
unset($_SESSION['profesor_student_ID']);
$stmt = $DB->execute_SELECT($query);
$note = array();
if (count($stmt) == 0) {
    $arr = array('Error' => 'No Records found!');
    echo json_encode($arr);
} else {
    $materie = '';
    $uname = '';
    foreach ($stmt as $row) {
        $info_nota = getfields($row['tip_nota']);
        $note[] = array('tip_nota' => $info_nota['tip'],
            'denumire_nota' => $info_nota['denumire'],
            'valoare' => $row['valoare'],
            'pondere' => $row['pondere']);
        $materie = $row['denumire'];
        $uname = $row['un'];
    }
    $arr = array('note' => $note, 'uname' => $uname, 'materie' => $materie, 'final_curs' => getNotaFinalaCurs($note), 'final_seminar_lab' => getNotaFinalaSeminarLab($note));
    echo json_encode($arr);
}
?>
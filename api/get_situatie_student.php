<?php
session_start();
include 'grades_classification.php';
include 'db.php';
$DB = new db();
if (!isset($_SESSION['profesor_ID_m']) || !isset($_SESSION['profesor_student_ID'])) {
    echo '<h1>Error!</h1><h3>Not found</h3>';
    die();
}
$query = "SELECT student.username AS un, tip_nota, pondere, valoare, materie.denumire, profesor.username FROM note INNER JOIN materie ON materie.id = note.materie_id INNER JOIN profesor on materie.profesor_id = profesor.id INNER JOIN student ON student.id = note.student_id WHERE note.student_id=" . $_SESSION['profesor_student_ID'] . " AND note.materie_id =" . $_SESSION['profesor_ID_m'] . " AND note.tip_nota NOT LIKE 'final_%'";
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
    $query = "SELECT student.username AS un, tip_nota, pondere, valoare, materie.denumire, profesor.username FROM note INNER JOIN materie ON materie.id = note.materie_id INNER JOIN profesor on materie.profesor_id = profesor.id INNER JOIN student ON student.id = note.student_id WHERE note.student_id=" . $_SESSION['profesor_student_ID'] . " AND note.materie_id =" . $_SESSION['profesor_ID_m'] . " AND note.tip_nota LIKE 'final_%'";
    $stmt = $DB->execute_SELECT($query);
    if (count($stmt) > 0) {
        foreach ($stmt as $row) {
            $fields = getfields($row['tip_nota']);
            if ($fields[1] == 'curs') {
                $ponderi = array('curs' => (int)$fields['pondere'], 'seminar_laborator' => 100 - (int)$fields['pondere']);
            } else {
                $ponderi = array('curs' => 100 - (int)$fields['pondere'], 'seminar_laborator' => (int)$fields['pondere']);
            }
        }
    }
}

$arr = array(note' => $note, 'uname' => $uname, 'materie' => $materie, 'final_curs' => getNotaFinalaCurs($note), 'final_seminar_lab' => getNotaFinalaSeminarLab($note), 'ponderi' => $ponderi);
echo json_encode($arr);

?>
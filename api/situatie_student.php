<?php
session_start();
include 'grades_classification.php';
include 'db.php';
$DB = new db();
if (!isset($_SESSION['ID_m'])) {
    echo '<h1>Error!</h1><h3>Not found</h3>';
    die();
}
$query = "SELECT tip_nota, pondere, valoare, materie.id, materie.denumire, profesor.username FROM note INNER JOIN materie ON materie.id = note.materie_id INNER JOIN profesor on materie.profesor_id = profesor.id WHERE materie.id=" . $_SESSION['ID_m'];
unset($_SESSION['ID_m']);
$stmt = $DB->execute_SELECT($query);
$note = array();
if (count($stmt) == 0) {
    $arr = array('Error' => 'No Records found!');
    echo json_encode($arr);
    //  response(NULL, NULL, 200,"No Record Found");
} else {
    foreach ($stmt as $row) {
        $info_nota = getfields($row['tip_nota']);
        $note[] = array('tip_nota' => $info_nota['tip'],
            'denumire_nota' => $info_nota['denumire'],
            'valoare' => $row['valoare'],
            'idm' => $row['id'],
            'denumire' => $row['denumire'],
            'username' => $row['username'],
            'pondere' => $row['pondere']);
    }

    $arr = array('note' => $note, 'id_materie' => $note[0]['idm'], 'uname' => $_SESSION['email'], 'nota_curs' => getNotaFinalaCurs($note), 'nota_seminar_laborator' => getNotaFinalaSeminarLab($note)); //'formula_curs' => getPonderiCurs($note), 'formula_seminar_laborator' => getPonderiSeminar_Laborator($note));

    echo json_encode($arr);
}
?>
<?php
function note_student()
{
    include 'grades_classification.php';
    $DB = new db();
    if (!isset($_SESSION['ID_m'])) {
        return json_encode(array('Error' => 'Not found'));
    }
    $query = "SELECT * FROM note
INNER JOIN student
ON note.student_id = student.id
INNER JOIN materie
ON materie.id = note.materie_id
WHERE student.username = '" . $_SESSION['email'] . "' AND materie.id = " . $_SESSION['ID_m'];
    $stmt = $DB->execute_SELECT($query);
    $note = array();
    $profesor = '';
    if (count($stmt) == 0) {
        $arr = array('Error' => 'No Records found!');
        return json_encode($arr);
        //  response(NULL, NULL, 200,"No Record Found");
    } else {
        foreach ($stmt as $row) {
            $info_nota = getfields($row['tip_nota']);
            $note[] = array('tip_nota' => $info_nota['tip'],
                'denumire_nota' => $info_nota['denumire'],
                'valoare' => $row['valoare'],
                'idm' => $row['id'],
                'denumire' => $row['denumire'],
                'pondere' => $row['pondere']);
        }
        $query = "SELECT student.username AS un, profesor.username AS unp, tip_nota, pondere, valoare, materie.denumire, profesor.username FROM note INNER JOIN materie ON materie.id = note.materie_id INNER JOIN profesor on materie.profesor_id = profesor.id INNER JOIN student ON student.id = note.student_id WHERE student.username='" . $_SESSION['email'] . "' AND note.materie_id =" . $_SESSION['ID_m'] . " AND note.tip_nota LIKE 'final_%'";
        $stmt = $DB->execute_SELECT($query);
        $prof = '';
        if (count($stmt) > 0) {
            foreach ($stmt as $row) {
                if (explode('_', $row['tip_nota'])[1] == 'curs') {
                    $ponderi = array('curs' => (int)$row['pondere'], 'seminar_laborator' => 100 - (int)$row['pondere']);
                } else {
                    $ponderi = array('curs' => 100 - (int)$row['pondere'], 'seminar_laborator' => (int)$row['pondere']);
                }
                $profesor = $row['unp'];
            }
            $arr = array('profesor' => $profesor, 'ponderi' => $ponderi, 'note' => $note, 'id_materie' => $note[0]['idm'], 'uname' => $_SESSION['email'], 'nota_curs' => getNotaFinalaCurs($note), 'nota_seminar_laborator' => getNotaFinalaSeminarLab($note)); //'formula_curs' => getPonderiCurs($note), 'formula_seminar_laborator' => getPonderiSeminar_Laborator($note));
        } else         $arr = array('profesor' => $profesor, 'note' => $note, 'id_materie' => $note[0]['idm'], 'uname' => $_SESSION['email'], 'nota_curs' => getNotaFinalaCurs($note), 'nota_seminar_laborator' => getNotaFinalaSeminarLab($note)); //'formula_curs' => getPonderiCurs($note), 'formula_seminar_laborator' => getPonderiSeminar_Laborator($note));
        return json_encode($arr);
    }
}
?>
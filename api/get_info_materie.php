<?php
session_start();
include 'grades_classification.php';
include 'db.php';


function getStudentiInscrisi($DB)
{
    $query = "SELECT materie.id, COUNT(DISTINCT(student.id)) AS numar FROM note INNER JOIN student ON note.student_id = student.id INNER JOIN materie ON note.materie_id = materie.id GROUP BY materie.id HAVING materie.id = " . $_SESSION['profesor_ID_m'];
    $stmt = $DB->execute_SELECT($query);
    if (count($stmt) == 0) {
        $arr = array('Error' => 'No Records found!');
        return $arr;
    } else {

        foreach ($stmt as $row) {
            $data = array('nr_inscrisi' => $row['numar']);
        }
        return $data;
    }
}

function getTitularCurs($DB)
{
    $query = "SELECT profesor.username FROM profesor
INNER JOIN materie on profesor.id = materie.profesor_id
WHERE materie.id = " . $_SESSION['profesor_ID_m'];
    $stmt = $DB->execute_SELECT($query);
    if (count($stmt) == 0) {
        $arr = array('Error' => 'No Records found!');
        return $arr;
    } else {

        foreach ($stmt as $row) {
            $data = array('prof_titular' => $row['username']);
        }
        return $data;
    }
}

function getMaterie($DB)
{
    $query = "SELECT materie.denumire FROM materie WHERE materie.id = " . $_SESSION['profesor_ID_m'];
    $stmt = $DB->execute_SELECT($query);
    if (count($stmt) == 0) {
        $arr = array('Error' => 'No Records found!');
        return $arr;
    } else {

        foreach ($stmt as $row) {
            $data = array('materie' => $row['denumire']);
        }
        return $data;
    }
}

function get_info_materie()
{
    $DB = new db();
    if (!isset($_SESSION['profesor_ID_m'])) {
        echo '<h1>Error!</h1><h3>Not found</h3>';
        die();
    }
    $arr = array('inscrisi' => getStudentiInscrisi($DB)['nr_inscrisi'], 'titular' => getTitularCurs($DB)['prof_titular'], 'materie' => getMaterie($DB)['materie']);
    return json_encode($arr);
}
?>
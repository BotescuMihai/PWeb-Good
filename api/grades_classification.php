<?php

function getfields($grade)
{
    $fields = explode("_", $grade);
    $name = '';
    $i = 0;
    foreach ($fields as $field) {
        if ($i > 0) {
            $name .= $field . ' ';
        }
        $i++;
    }
    return array('tip' => $fields[0], 'denumire' => $name);
}

function getNotaFinalaCurs($note)
{
    $sum = 0;
    foreach ($note as $nota) {
        if ($nota['tip_nota'] == 'curs') {
            $sum += $nota['valoare'] * ($nota['pondere'] / 100);
        }
    }
    return $sum;
}

function getPonderiCurs($note)
{
    $final = '';
    foreach ($note as $nota) {
        if ($nota['tip_nota'] == 'curs') {
            $denumiri = getfields($nota);
            $denumire = $denumiri['denumire'];
            $final = $final . $denumire . '(' . $nota['pondere'] . '%), ';
        }
    }
    return $final;
}

function getPonderiSeminar_Laborator($note)
{
    $final = '';
    foreach ($note as $nota) {
        if ($nota['tip_nota'] == 'seminar' || $nota['tip_nota'] == 'laborator') {
            $denumiri = getfields($nota);
            $denumire = $denumiri['denumire'];
            $final = $final . $denumire . '(' . $nota['pondere'] . '%), ';
        }
    }
    return $final;
}

function getNotaFinalaSeminarLab($note)
{
    $sum = 0;
    foreach ($note as $nota) {
        if ($nota['tip_nota'] == 'seminar' || $nota['tip_nota'] == 'laborator') {
            $sum += $nota['valoare'] * ($nota['pondere'] / 100);
        }
    }
    return $sum;
}



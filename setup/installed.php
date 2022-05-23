<?php
session_start();

class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('../api/pw.db');
    }

}

if (isset($_POST['next'])) {
    /*  enter your
     sql code for
     teble creating */
    if (isset($_SESSION['PDO']))
        if ($_SESSION['PDO'] == 'sqlite3') {
            $db = new MyDB();
            $sql = <<<EOF


CREATE TABLE IF NOT EXISTS `materie` (
  `id` int(10) NOT NULL PRIMARY KEY,
  `denumire` varchar(150) NOT NULL,
  `profesor_id` int(10) NOT NULL 
);

DELETE FROM materie;

INSERT INTO `materie` (`id`, `denumire`, `profesor_id`) VALUES
(1, 'Sisteme de Operare I', 1),
(2, 'PCD', 1),
(3, 'Programare WEB', 1);


CREATE TABLE IF NOT EXISTS `note` (
  `id` int(10) NOT NULL,
  `materie_id` int(10) NOT NULL,
  `student_id` int(10) NOT NULL,
  `tip_nota` varchar(100) NOT NULL,
  `valoare` float NOT NULL DEFAULT 0,
  `pondere` float NOT NULL DEFAULT 0
);

DELETE FROM note;

INSERT INTO `note` (`id`, `materie_id`, `student_id`, `tip_nota`, `valoare`, `pondere`) VALUES
(2, 1, 1, 'laborator_test_#1', 9, 25),
(5, 2, 1, 'curs_Proiect_Curs', 10, 50),
(6, 2, 1, 'seminar_laborator_Test_#1', 10, 20),
(8, 1, 1, 'final_curs', 0, 40),
(9, 1, 1, 'seminar_laborator_Test_#2', 0, 0),
(10, 1, 1, 'seminar_laborator_Test_#2', 0, 0),
(11, 1, 1, 'curs_Examen_(C)', 0, 50),
(16, 1, 1, 'seminar_laborator_Test_#1', 0, 0),
(17, 1, 2, 'seminar_laborator_Test_#1', 0, 0),
(18, 1, 1, 'seminar_laborator_Test_#3', 0, 0),
(19, 1, 2, 'seminar_laborator_Test_#3', 0, 0),
(20, 2, 3, 'seminar_laborator_Test_#2', 0, 0),
(21, 2, 3, 'curs_Test_#2', 0, 0),
(22, 1, 1, 'curs_partial_1', 0, 50),
(23, 1, 2, 'curs_partial_1', 0, 50),
(24, 1, 1, 'curs_Partial_4', 0, 50),
(25, 1, 2, 'curs_Partial_4', 0, 50),
(26, 2, 3, 'curs_Lucrare_(MAI_2022)', 0, 50),
(27, 2, 3, 'curs_Lucrare', 0, 50),
(28, 3, 4, 'curs_Examen_(A2)_--_Proiect', 10, 100),
(29, 3, 4, 'seminar_laborator_Sprint_1', 10, 33),
(30, 3, 4, 'seminar_laborator_Sprint_2', 10, 34),
(31, 3, 4, 'seminar_laborator_Sprint_3', 10, 33);


CREATE TABLE IF NOT EXISTS `prezenta` (
  `id` int(10) NOT NULL PRIMARY KEY,
  `materie_id` int(10) NOT NULL,
  `student_id` int(10) NOT NULL,
  `tip_prezenta` varchar(150) NOT NULL,
  `numar_prezente` int(10) NOT NULL DEFAULT 0
);


DELETE FROM prezenta;


INSERT INTO `prezenta` (`id`, `materie_id`, `student_id`, `tip_prezenta`, `numar_prezente`) VALUES
(1, 1, 1, 'curs', 14),
(2, 1, 1, 'seminar_laborator', 14),
(3, 1, 2, 'curs', 10),
(4, 1, 2, 'seminar_laborator', 14);

CREATE TABLE IF NOT EXISTS `profesor` (
  `id` int(10) NOT NULL PRIMARY KEY,
  `username` varchar(150) NOT NULL
);

DELETE FROM profesor;


INSERT INTO `profesor` (`id`, `username`) VALUES
(1, 'florin.fortis@e-uvt.ro');

CREATE TABLE IF NOT EXISTS `student` (
  `id` int(10) NOT NULL PRIMARY KEY,
  `username` varchar(150) NOT NULL,
  `materie_ID` int(10) NOT NULL
);

DELETE FROM student;

INSERT INTO `student` (`id`, `username`, `materie_ID`) VALUES
(1, 'mihai.botescu00@e-uvt.ro', 1),
(2, 'robert.beze00@e-uvt.ro', 1),
(3, 'mihai.botescu00@e-uvt.ro', 2),
(4, 'mihai.botescu00@e-uvt.ro', 3);
EOF;

            if (!$db) {
                echo $db->lastErrorMsg();
            } else {
                echo "Opened database successfully\n";
            }

            $ret = $db->exec($sql);
            if (!$ret) {
                echo $db->lastErrorMsg();
            } else {
                echo "Table created successfully\n";
            }
        }

    echo "<h2 align=center >Finished</h2>";
} else {
    echo "<h2 align=center >Error</h2>";
}
?>

<?php
session_start();
include '../api/email_validation.php';
include '../api/db.php';
include '../api/grades_classification.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

//$email = '';

$app = new \Slim\App;

//twig
/*
$loader = new \Twig\Loader\FilesystemLoader('resources/views');
$twig = new \Twig\Environment($loader, [
    'cache' => 'resources/cache',
]); */


/////
$container = $app->getContainer();

$container['view'] = function ($container) {

    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [

        'cache' => false,

    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(

        $container->router,

        $container->request->getUri()

    ));

    return $view;

};


$app->get('/profesor/materii', function (Request $request, Response $response) {
    $DB = new db();

    if (!isset($_SESSION['email'])) {
        $arr = array('error' => 'You must be logged in !');
        echo json_encode($arr);
        die();
    }
    $query = "SELECT materie.id, denumire FROM materie INNER JOIN profesor WHERE materie.profesor_id=profesor.id AND profesor.username='" . $_SESSION['email'] . "'";
    $stmt = $DB->execute_SELECT($query);
    $materii = array();
    if (count($stmt) == 0) {
        $arr = array('Error' => 'No Records found!');
        echo json_encode($arr);
        die();
    } else {
        foreach ($stmt as $row) {
            $materii[] = array('id' => $row['id'], 'denumire' => $row['denumire'], 'email' => $_SESSION['email']);
        }
        echo json_encode($materii);
    }
});

$app->get('/teacher/subjects', function (Request $request, Response $response) {
    return $this->view->render($response, 'courses_teacher.twig');
});


$app->get('/student/materii', function (Request $request, Response $response) {
    $DB = new db();
    $payload = array();
    if (!isset($_SESSION['email'])) {
        $arr = array('error' => 'You must be logged in !');
        echo json_encode($arr);
    } else {
        $query = "SELECT materie.id, denumire from materie
INNER JOIN student ON materie.id = student.materie_ID
WHERE student.username ='" . $_SESSION['email'] . "'";
        $stmt = $DB->execute_SELECT($query);
        $materii = array();
        if (count($stmt) == 0) {
            $arr = array('Error' => 'No Records found!', 'email' => $_SESSION['email']);
            $payload = $arr;
//        echo $payload;
        } else {
            foreach ($stmt as $row) {
                $materii[] = array('id' => $row['id'], 'denumire' => $row['denumire'], 'email' => $_SESSION['email']);
            }
            $payload = $materii;
            //   echo $payload;
        }
        echo json_encode($payload);
    }
});

$app->get('/student/courses', function (Request $request, Response $response) {
    return $this->view->render($response, 'courses_student.twig');
});

$app->get('/student/materii/{ID_m}/note', function (Request $req, Response $resp, $args) {
    $_SESSION['ID_m'] = $args['ID_m'];
    $DB = new db();
    if (!isset($_SESSION['ID_m'])) {
        return json_encode(array('Error' => 'Not found'));
    }
    if (!isset($_SESSION['email'])) {
        return json_encode(array('Error' => 'You must be logged in!'));
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
        echo json_encode($arr);
    }
    //  include '../api/situatie_student.php';
//    $arr = note_student();
    //  echo $arr;
});

$app->get('/student/courses/{ID_m}/grades', function (Request $req, Response $resp, $args) {
    $this->view->render($resp, 'note_student.twig', ['ID_m' => $args['ID_m']]);
});

/*
// /profesor/materii/{ID}/note

$app->get('/teacher/courses/{ID}/grades', function (Request $request, Response $response) {
    if (!isset($_SESSION['email'])) { // daca nu is autentificat
        $newresponse = $response->withStatus(404);
        throw new PDOException('Not logged in');
    }
    return $this->view->render($response, 'course_grades.twig');
});

$app->get('/profesor/materii/{ID}/note', function (Request $request, Response $response) {

}); */
$app->get('/teacher/subjects/{ID_m}', function (Request $request, Response $response, $args) {
    if (!isset($_SESSION['email'])) { // daca nu is autentificat
        $newresponse = $response->withStatus(404);
        throw new PDOException('Not logged in');
    }
    $_SESSION['profesor_ID_m'] = $args['ID_m'];
    return $this->view->render($response, 'course_info.twig', ['profesor_ID_m' => $args['ID_m']]);
});

$app->get('/profesor/materii/{ID_m}', function (Request $request, Response $response, $args) {
    $DB = new db();
    $ID_m = $args['ID_m'];
    $arr = array('inscrisi' => getStudentiInscrisi($DB, $ID_m)['nr_inscrisi'], 'titular' => getTitularCurs($DB, $ID_m)['prof_titular'], 'materie' => getMaterie($DB, $ID_m)['materie']);
    if ($arr['inscrisi'] == null && $arr['titular'] == null && $arr['materie'] == null) {
        echo json_encode(array('Error' => 'No records found!'));
    } else if ($arr['inscrisi'] == null) {
        $arr['inscrisi'] = 0;
        echo json_encode($arr);
    } else
        echo json_encode($arr);
});

//////
///


$app->get('/teacher/courses/{ID_m}/students', function ($request, $response, $args) {
    $_SESSION['profesor_ID_m'] = $args['ID_m'];
    return $this->view->render($response, 'student_grades.twig', ['ID_m' => $args['ID_m']]);
});

$app->get('/profesor/materii/{ID_m}/studenti', function ($request, $response, $args) {
    $_SESSION['profesor_ID_m'] = $args['ID_m'];
    $DB = new db();

    $query = "SELECT DISTINCT student.id, materie.id AS idm, student.username FROM student INNER JOIN note on note.student_id = student.id INNER JOIN materie on note.materie_id = materie.id WHERE materie.id=" . $_SESSION['profesor_ID_m'];
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
            $studenti[] = array('id' => $row['id'], 'username' => $row['username'], 'id_m' => $row['idm']);
        }
        echo json_encode($studenti);
    }
});
/*

$app->get('/profesor/materii/{ID_m}/studenti/{student_ID}/note', function (Request $request, Response $response, $args) {
    $DB = new db();
    $stmt = $DB->execute_SELECT('SELECT student.username, note.tip_nota, note.valoare, note.pondere FROM note INNER JOIN student ON note.student_id = student.id INNER JOIN materie ON note.materie_id = materie.id WHERE student.id=' . $args['student_ID'] . ' AND materie.id=' . $args['ID_m']);
    if (count($stmt) == 0) {
        $arr = array('Error' => 'No Records found!');
        echo json_encode($arr);
    } else {
        $note = array();
        foreach($stmt as $row){
            $email = $row['username'];
            $note[] = array('tip'=>$row['tip_nota'], 'valoare'=>$row['valoare'], 'pondere'=>$row['pondere']);
        }
        echo json_encode(array('email'=>$email, 'note'=>$note));
    }
});
 */
///////////

$app->get('/teacher/courses/{ID_m}/students/{ID}/grades', function ($request, $response, $args) {
    return $this->view->render($response, 'student_grades.twig', ['student_ID' => $args['ID'], 'ID_m' => $args['ID_m']]);
});

$app->get('/profesor/materii/{ID_m}/studenti/{ID}/note', function ($request, $response, $args) {
    /*
    $DB = new db();
    $stmt = $DB->execute_SELECT('SELECT student.username, note.tip_nota, note.valoare, note.pondere FROM note INNER JOIN student ON note.student_id = student.id INNER JOIN materie ON note.materie_id = materie.id WHERE student.id=' . $args['ID'] . ' AND materie.id=' . $args['ID_m']);
    if (count($stmt) == 0) {
        $arr = array('Error' => 'No Records found!');
        echo json_encode($arr);
    } else {
        $note = array();
        foreach($stmt as $row){
            $email = $row['username'];
            $note[] = array('tip_nota'=>getfields($row['tip_nota'])['tip'], 'denumire_nota'=>getfields($row['tip_nota'])['denumire'], 'valoare'=>$row['valoare'], 'pondere'=>$row['pondere']);
        }
        echo json_encode(array('uname'=>$email, 'note'=>$note));
    } **/

    //////////////////////////////////////////////
    ///
    ///
    $DB = new db();
    $query = "SELECT student.username AS un, tip_nota, pondere, valoare, materie.denumire, profesor.username FROM note INNER JOIN materie ON materie.id = note.materie_id INNER JOIN profesor on materie.profesor_id = profesor.id INNER JOIN student ON student.id = note.student_id WHERE note.student_id=" . $args['ID'] . " AND note.materie_id =" . $args['ID_m'] . " AND note.tip_nota NOT LIKE 'final_%'";
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
        $query = "SELECT student.username AS un, tip_nota, pondere, valoare, materie.denumire, profesor.username FROM note INNER JOIN materie ON materie.id = note.materie_id INNER JOIN profesor on materie.profesor_id = profesor.id INNER JOIN student ON student.id = note.student_id WHERE note.student_id=" . $args['ID'] . " AND note.materie_id =" . $args['ID_m'] . " AND note.tip_nota LIKE 'final_%'";
        $stmt = $DB->execute_SELECT($query);
        if (count($stmt) > 0) {
            foreach ($stmt as $row) {
                $fields = getfields($row['tip_nota']);
                if ($fields['tip'] == 'curs') {
                    $ponderi = array('curs' => (int)$row['pondere'], 'seminar_laborator' => 100 - (int)$row['pondere']);
                } else {
                    $ponderi = array('curs' => 100 - (int)$row['pondere'], 'seminar_laborator' => (int)$row['pondere']);
                }
            }
        }
    }
    if (isset($ponderi))
        $arr = array('note' => $note, 'uname' => $uname, 'materie' => $materie, 'final_curs' => getNotaFinalaCurs($note), 'final_seminar_lab' => getNotaFinalaSeminarLab($note), 'ponderi' => $ponderi);
    else $arr = array('note' => $note, 'uname' => $uname, 'materie' => $materie, 'final_curs' => getNotaFinalaCurs($note), 'final_seminar_lab' => getNotaFinalaSeminarLab($note), 'ponderi' => 'Nu au fost setate inca!');
    echo json_encode($arr);
});

///////////////

$app->get('/', function ($request, $response) {
    $this->view->render($response, 'login.twig'); //pagina principala de login
});

$app->post('/', function (Request $request, Response $response, array $args) {
    $router = $this->router;
    $_SESSION['email'] = $_POST['email'];
    //$_SESSION['email'] = $_POST['email'];
    if (profesor($_POST['email'])) {
        return $response->withRedirect($router->pathFor('profesor-login'))->withAddedHeader('email', $_POST['email']);
    } else         return $response->withRedirect($router->pathFor('student-login'))->withAddedHeader('email', $_POST['email']);

});

$app->get('/student', function (Request $request, Response $response, array $args) {
    return $this->view->render($response, 'courses_student.twig');
})->setName('student-login');

$app->get('/teacher', function (Request $request, Response $response, array $args) {
    return $this->view->render($response, 'courses_teacher.twig');
})->setName('profesor-login');

/////////////////
///
$app->get('/teacher/subjects/{ID_m}/students/{student_ID}/attendance/{tip}', function (Request $request, Response $response, $args) {
    if (!isset($_SESSION['email'])) { // daca nu is autentificat
        $newresponse = $response->withStatus(404);
        throw new PDOException('Not logged in');
    }
    $_SESSION['profesor_ID_m'] = $args['ID_m'];
    $_SESSION['profesor_student_ID'] = $args['student_ID'];
    $_SESSION['profesor_tip_prezenta'] = $args['tip'];
    return $this->view->render($response, 'prezente_student.twig', ['ID_m' => $args['ID_m'], 'student_ID' => $args['student_ID'], 'tip' => $args['tip']]);
});

$app->get('/profesor/materii/{ID_m}/studenti/{student_ID}/prezente/{tip}', function (Request $request, Response $response, $args) {
    $DB = new db();
    $_SESSION['profesor_ID_m'] = $args['ID_m'];
    $_SESSION['profesor_student_ID'] = $args['student_ID'];
    $_SESSION['profesor_tip_prezenta'] = $args['tip'];
    if (!isset($_SESSION['profesor_ID_m']) || !isset($_SESSION['profesor_student_ID']) || !isset($_SESSION['profesor_tip_prezenta'])) {
        echo '<h1>Error!</h1><h3>Not found</h3>';
        die();
    }
    $query = "SELECT materie.denumire, student.id AS sid, materie.id AS mid, student.username, prezenta.tip_prezenta, prezenta.numar_prezente
FROM prezenta
INNER JOIN student ON prezenta.student_id = student.id
INNER JOIN materie ON prezenta.materie_id = materie.id
WHERE prezenta.materie_id = " . $_SESSION['profesor_ID_m'] . " AND student.id = " . $_SESSION['profesor_student_ID'] . " AND prezenta.tip_prezenta='" . $_SESSION['profesor_tip_prezenta'] . "'";
    $stmt = $DB->execute_SELECT($query);
    $note = array();
    if (count($stmt) == 0) {
        $arr = array('Error' => 'No Records found!');
        echo json_encode($arr);
    } else {
        foreach ($stmt as $row) {
            $prezente = array('username' => $row['username'], 'materie' => $row['denumire'], 'student_id' => $row['sid'], 'materie_id' => $row['mid'], 'tip' => $row['tip_prezenta'], 'numar' => $row['numar_prezente']);
        }
        echo json_encode($prezente);
    }
});

//////////////////

$app->get('/student/courses/{ID_m}/attendance', function (Request $request, Response $response, $args) {
    if (!isset($_SESSION['email'])) { // daca nu is autentificat
        $newresponse = $response->withStatus(404);
        throw new PDOException('Not logged in');
    }
    return $this->view->render($response, 'prezente_student_all.twig', ['ID_m' => $args['ID_m']]);
});

$app->get('/student/materii/{ID_m}/prezente', function (Request $request, Response $response, $args) {
    $DB = new db();
    $query = "SELECT materie.denumire, student.id AS sid, materie.id AS mid, student.username, prezenta.tip_prezenta, prezenta.numar_prezente
FROM prezenta
INNER JOIN student ON prezenta.student_id = student.id
INNER JOIN materie ON prezenta.materie_id = materie.id
WHERE prezenta.materie_id = " . $args['ID_m'] . " AND student.username='" . $_SESSION['email'] . "'";
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
});


///////////// POST


$app->post('/profesor/materii/{ID_m}/studenti/note', function (Request $request, Response $response, array $args) {
    $ID_m = $args['ID_m'];
    $nota = $_REQUEST['nota'];
    $db = new db();
    $stmt = $db->execute_SELECT("SELECT student.username, student.id
    FROM student
    INNER JOIN materie ON materie.id = student.materie_ID
    WHERE student.materie_ID=" . $ID_m);
    if (count($stmt) == 0) {
        echo json_encode(array('Eroare' => 'Nu aveti studenti inscrisi la materia Dvs.!'));
        die();
    } else {
        $new_stmt = $db->execute_SELECT("SELECT note.tip_nota FROM note WHERE note.materie_id=" . $ID_m . " AND note.tip_nota='" . $nota . "'");
        if (count($new_stmt) > 0) { // daca o nota exista deja, n-o mai adaug.
            echo json_encode(array('Error' => 'Record already exists! It will not be added...'));
            die();//
        }
        $studenti = array();
        foreach ($stmt as $row) {
            $studenti[] = $row['id'];
        }
        echo json_encode($studenti);
        $db = new db();
        $stmt = $db->execute_SELECT("SELECT id FROM note ORDER BY id DESC");
        $id = 0;
        foreach ($stmt as $row) {
            $id = $row['id'] + 1;
            break;
        }
        $pdo = new PDO("mysql:host=localhost;dbname=proiectpw", "root", "root");
        $stmt = $pdo->prepare("INSERT INTO note(id, materie_id, student_id, tip_nota, valoare, pondere)  VALUES (:id, :materie_id, :student_id, :tip_nota, :valoare, :pondere)");
        foreach ($studenti as $student) {
            $stmt->execute(array(':id' => $id, ':materie_id' => $ID_m, ':student_id' => $student, ':tip_nota' => $nota, ':valoare' => 0, ':pondere' => 0));
            $id++;
        }
    }
});


/////////////////////// PUT
/*
$app->put('/profesor/materii/{ID_m}/note/{nota_ID}', function (Request $request, Response $response, array $args) {
    $ID_m = $args['ID_m'];
    $nota_ID = $args['nota_ID'];
    $pdo = new PDO("mysql:host=localhost;dbname=proiectpw", "root", "root");
    $stmt = $pdo->prepare("UPDATE note SET id=(:id) WHERE id=" . $nota_ID . " AND materie_id=" . $ID_m);
});
*/

///////////////////// DELETE

$app->delete('/profesor/materii/{ID_m}/note/{nota_ID}', function (Request $request, Response $response, array $args) {
    $ID_m = $args['ID_m'];
    $nota_ID = $args['nota_ID'];
    $pdo = new PDO("mysql:host=localhost;dbname=proiectpw", "root", "root");
    $stmt = $pdo->prepare("DELETE FROM note WHERE materie_id=:mid AND tip_nota=:idd");
    $stmt->execute(array(':mid' => $ID_m, ':idd' => $nota_ID));
});


//////////////// helper functions
function getStudentiInscrisi($DB, $ID_m)
{
    $query = "SELECT materie.id, COUNT(DISTINCT(student.id)) AS numar FROM note INNER JOIN student ON note.student_id = student.id INNER JOIN materie ON note.materie_id = materie.id GROUP BY materie.id HAVING materie.id = " . $ID_m;
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

function getTitularCurs($DB, $ID_m)
{
    $query = "SELECT profesor.username FROM profesor
INNER JOIN materie on profesor.id = materie.profesor_id
WHERE materie.id = " . $ID_m;
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

function getMaterie($DB, $ID_m)
{
    $query = "SELECT materie.denumire FROM materie WHERE materie.id = " . $ID_m;
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


$app->run();
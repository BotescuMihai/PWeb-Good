<?php
session_start();
include '../api/email_validation.php';
include '../api/db.php';

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


$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    echo json_encode(array('hello' => $name));
});



$app->get('/testt', function (Request $request, Response $response) {
    return $this->view->render($response, 'test.twig');
});

$app->get('/home', function (Request $request, Response $response) {
    $lista = array('today' => 'joi', 'tomorrow' => 'friday');
    $context = array('zile' => $lista);
    return $this->view->render($response, 'home.twig', $context);
});

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
        $payload = $arr;
        //   echo $payload;
    }
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

});

$app->get('/student/courses', function (Request $request, Response $response) {
    return $this->view->render($response, 'courses_student.twig');
});

$app->get('/student/materii/{ID_m}/note', function (Request $req, Response $resp, $args) {
    $_SESSION['ID_m'] = $args['ID_m'];
    //  include '../api/situatie_student.php';
//    $arr = note_student();
    //  echo $arr;
});

$app->get('/student/courses/{ID_m}/grades', function (Request $req, Response $resp, $args) {
    $_SESSION['ID_m'] = $args['ID_m'];
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
    if (!isset($_SESSION['profesor_ID_m'])) {
        echo json_encode(array('Error' => 'Not found'));
        die();
    }
    $arr = array('inscrisi' => getStudentiInscrisi($DB)['nr_inscrisi'], 'titular' => getTitularCurs($DB)['prof_titular'], 'materie' => getMaterie($DB)['materie']);
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

$app->get('/profesor/materii/{ID_m}/studenti/{student_ID}/note', function ($request, $response, $args) {

});

///////////

$app->get('/teacher/subjects/{ID_m}/students/grades', function ($request, $response, $args) {
    $_SESSION['profesor_ID_m'] = $args['ID_m'];
    return $this->view->render($response, 'student_grades.twig');
});

$app->get('/profesor/materii/{ID_m}/studenti/{ID}/note', function ($request, $response, $args) {
    $_SESSION['profesor_ID_m'] = $args['ID_m'];
    $DB = new db();
    $query = "SELECT DISTINCT student.id, materie.id AS idm, student.username FROM student INNER JOIN note on note.student_id = student.id INNER JOIN materie on note.materie_id = materie.id WHERE materie.id=" . $_SESSION['profesor_ID_m'] . " AND student.id=" . $args['ID'];
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

///////////////

$app->get('/', function ($request, $response) {
    $this->view->render($response, 'login.twig'); //pagina principala de login
});

$app->post('/', function (Request $request, Response $response, array $args) {
    $router = $this->router;
    $_SESSION['email'] = $_POST['email'];
    if (profesor($_SESSION['email'])) {
        return $response->withRedirect($router->pathFor('profesor-login'));
    } else return $response->withRedirect($router->pathFor('student-login'));
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
    $_SESSION['student_ID_m'] = $args['ID_m'];
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


//////////////// helper functions
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


$app->run();
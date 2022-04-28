<?php
session_start();
include '../api/db.php';
include '../api/grades_classification.php';
include '../api/email_validation.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$email = '';

$app = new \Slim\App;

//twig
/*
$loader = new \Twig\Loader\FilesystemLoader('resources/views');
$twig = new \Twig\Environment($loader, [
    'cache' => 'resources/cache',
]);
*/


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
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->post('/', function (Request $request, Response $response, array $args) {
    $router = $this->router;
    $_SESSION['email'] = $_POST['email'];
    if (profesor($_SESSION['email'])) {
        //   $response->getBody()->write('Profesor');
        //  return $response;
        return $response->withRedirect($router->pathFor('profesor-materii', ['email' => $_SESSION['email']]));
    } else {
        //   $response->getBody()->write('Student');
        // return $response;
        return $response->withRedirect($router->pathFor('student-materii', ['email' => $_SESSION['email']]));
    }
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
//    $context = array('email' => $email);
    $DB = new db();
    $stmt = $DB->execute_SELECT("SELECT denumire FROM materie INNER JOIN profesor WHERE materie.profesor_id=profesor.id AND profesor.username='" . $_SESSION['email'] . "'");
    $denumiri = array();
    foreach ($stmt as $row) {
        $denumiri[] = $row['denumire'];
    }
    return $this->view->render($response, 'courses_teacher.twig', ['email' => $_SESSION['email'], 'denumiri' => $denumiri]);
})->setName('profesor-materii');


$app->get('/student/materii', function (Request $request, Response $response) {
//    $context = array('email' => $email);
    $DB = new db();
    $stmt = $DB->execute_SELECT("SELECT denumire FROM materie INNER JOIN profesor WHERE materie.profesor_id=profesor.id AND profesor.username='" . $_SESSION['email'] . "'");
    $denumiri = array();
    foreach ($stmt as $row) {
        $denumiri[] = $row['denumire'];
    }
    return $this->view->render($response, 'courses_student.twig', ['email' => $_SESSION['email'], 'denumiri' => $denumiri]);
})->setName('student-materii');


$app->get('/student/materii/{ID_m}/note', function (Request $req, Response $resp, $args) {
    if (!isset($_SESSION['email'])) {
        $resp->getBody()->write('Eroare! nu sunteti autentificat');
        return $resp;
    }
    $_SESSION['ID_m'] = $args['ID_m'];
    $this->view->render($resp, 'note_student.twig');
});
$app->get('/profesor/materii/{ID}/note', function (Request $request, Response $response) {
    if (!isset($_SESSION['email'])) { // daca nu is autentificat
        $newresponse = $response->withStatus(404);
        throw new PDOException('Not logged in');
    }
    return $this->view->render($response, 'course_grades.twig');
});

$app->get('/', function ($request, $response) {
    return $this->view->render($response, 'login.twig'); //pagina principala de login
});

$app->run();
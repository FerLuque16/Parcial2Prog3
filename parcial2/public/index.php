<?php

require __DIR__ . '/../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Clases\Usuario;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

use App\Controllers\UserController;
use App\Controllers\MateriaController;
use App\Controllers\Alumnos_MateriasController;
use App\Middlewares\AuthAdminMiddleware;
use App\Middlewares\AuthAlumnoMiddleWare;
use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\AuthProfesorMiddleware;
use App\Models\Alumnos_Materias;
use Config\Database;

new Database;


$app = AppFactory::create();
$app->setBasePath('/Programacion3/parcial2/public');
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();



$app->post('/login', UserController::class.':login');//->add(new AuthMiddleware);//Agregar metodo de verificacion al middleware

$app->post('/users', UserController::class.':add');//Cambiar metodo a uno de registro

$app->group('/materia', function (RouteCollectorProxy $group) {
    
    $group->post('[/]',MateriaController::class.":addMateria")->add(new AuthAdminMiddleware);

    $group->get('[/]',MateriaController::class.":getAll");

});

$app->group('/inscripcion',function (RouteCollectorProxy $group){

    $group->post('/{idMateria}',Alumnos_MateriasController::class.":addAlumno")->add(new AuthAlumnoMiddleWare);
    $group->get('/{idMateria}',Alumnos_MateriasController::class.":getMateria")->add(new AuthMiddleware);

});

$app->group('/notas',function (RouteCollectorProxy $group)
{
    $group->put('/{idMateria}',Alumnos_MateriasController::class.":addNota")->add(new AuthProfesorMiddleware);
    $group->get('/{idMateria}',Alumnos_MateriasController::class.":getNotas");
});



$app->add(new JsonMiddleware);




$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$app->run();



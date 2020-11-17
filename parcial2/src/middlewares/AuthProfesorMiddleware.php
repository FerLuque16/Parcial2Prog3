<?php
namespace App\Middlewares;

use App\Controllers\UserController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthProfesorMiddleware
{
    public function __invoke( Request $request, RequestHandler $handler)
    {
        $respuesta=UserController::PermitirPermiso($request->getHeaderLine('token'),'profesor');
        //$jwt = !true; //VALIDAR EL TOKEN

        if(!$respuesta)
        {
            $response = new Response();

            //$rta = array("rta"=>"Debe ser admin para tener acceso");

            $response ->getBody()->write(json_encode("Debe ser un profesor para realizar esa accion"));

            return $response;
        }
        else
        {
            $response= $handler->handle($request);
            $existingContent = (string)$response->getBody();

            $resp= new Response();

            $resp->getBody()->write($existingContent);

            return $resp;
               
        }


    }
}
<?php
namespace App\Middlewares;

use App\Controllers\UserController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware
{
    
    public function __invoke( Request $request, RequestHandler $handler)
    {
        $respuesta1=UserController::PermitirPermiso($request->getHeaderLine('token'),'admin');
        $respuesta2=UserController::PermitirPermiso($request->getHeaderLine('token'),'profesor');

        if(!$respuesta1 && !$respuesta2)
        {
            $response = new Response();

            

            $response ->getBody()->write(json_encode("Debe ser admin o profesor para realizar esta accion"));

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
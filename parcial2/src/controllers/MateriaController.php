<?php
namespace App\Controllers;

use App\Models\Materia;
use \Firebase\JWT\JWT;

use App\Controllers\UserController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MateriaController
{
    
    public function addMateria($request,$response, $args)
    {
        $materia = new Materia;

        $datos = $request->getParsedBody();

        $materia->nombre=$datos['materia'];
        $materia->cuatrimestre=$datos['cuatrimestre'];
        $materia->cupos=$datos['cupos'];

        if($materia->cuatrimestre<1 || $materia->cuatrimestre > 4)
        {
            $response->getBody()->write(json_encode("El cuatrimestre debe ser solo de 1 a 4"));
        }
        else
        {
            if($materia->cupos <=0)
            {
                $response->getBody()->write(json_encode("Los cupos deben ser mayor a 0"));
            }
            else
            {
                $materia->save();
                $response->getBody()->write(json_encode("Materia guardada correctamente"));
            }
        }


       

        return $response;
    
    }

    public function getAll($request, $response, $args)
    {
        $token=$request->getHeaderLine('token');
        if(!isset($token) || $token == "")
        {
            $response->getBody()->write(json_encode("Debe estar logueado para realizar esa accion"));
        }
        else
        {
            $materias = Materia::get();
        
            $response->getBody()->write(json_encode($materias));
        }
        
        return $response;
    }
}
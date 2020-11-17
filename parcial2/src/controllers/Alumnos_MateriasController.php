<?php
namespace App\Controllers;

use App\Models\Alumnos_Materias;
use App\Models\User;
use App\Models\Materia;
use \Firebase\JWT\JWT;

class Alumnos_MateriasController
{
    public function addAlumno($request, $response, $args)
    {
        $idMateria=$args['idMateria'];
        $idAlumno=UserController::ObtenerIdToken($request->getHeaderLine('token'));

        $materia=Materia::find($idMateria);
        if(isset($materia))
        {
            $alumno_materia=new Alumnos_Materias();

            $alumno_materia->idAlumno=$idAlumno;
            $alumno_materia->idMateria=$idMateria;
            $alumno_materia->save();

            $response->getBody()->write(json_encode("Se ha agregado el alumno correctamente"));
        }
        else
        {
            $response->getBody()->write(json_encode("No se ha encontrado una materia con ese id"));
        }

        
        return $response;
    }

    public function addNota($request, $response, $args)
    {
        $datos = $request->getParsedBody();
        $nota = $datos['nota'];        
        $idAlumno = $datos['idAlumno'];
        $idMateria = $args['idMateria'];

        

        $existe=User::where('id', $idAlumno)->where('tipo','alumno')->first();

        if(isset($existe))
        {
            if($nota<1 || $nota >10)
            {
                $response->getBody()->write(json_encode("La nota debe ser entre 1 y 10"));
            }
            else
            {
                $materia= Materia::find($idMateria);
                if(!isset($materia))
                {
                    $response->getBody()->write(json_encode("No existe una materia con ese id"));
                }
                else
                {
                    $respuesta = Alumnos_Materias::where('idAlumno',$idAlumno)->where('idMateria', $idMateria)->first();
                    if(!isset($respuesta))
                    {
                        $response->getBody()->write(json_encode("El alumno indicado no se encuentra inscripto en la materia indicada"));
                    }
                    else
                    {
                        $respuesta->nota=$nota;                    
    
                        $respuesta->save();
    
                        $response->getBody()->write(json_encode("Nota agregada correctamente"));
                    }
                }
               

               
            }
         
        }
        else
        {
            $response->getBody()->write(json_encode("No existe un alumno con el id indicado"));
        }

       
        

        return $response;
    }

    public function getMateria($request, $response, $args)
    {
            $idMateria = $args['idMateria'];

            $materia = Materia::find($idMateria);
            if(!isset($materia))
            {
                $response->getBody()->write(json_encode("No existe una materia con ese id"));
            }
            else
            {
                $datos=Alumnos_Materias:: join('usuarios', 'usuarios.id', '=', 'alumnos_materias.idAlumno')
                ->join('materias', 'materias.id', '=', 'alumnos_materias.idMateria')
                ->select('materias.nombre as Nombre de la Materia','usuarios.nombre as Nombre')
                ->where('materias.id',$idMateria)
                ->get();

                $response->getBody()->write(json_encode($datos));
            }

            

            return $response;
    }

    public function getNotas($request, $response, $args)
    {
        $idMateria = $args['idMateria'];

        $materia = Materia::find($idMateria);

        $token = $request->getHeaderLine('token');
        if(!isset($token) || $token=="")
        {
            $response->getBody()->write(json_encode("Debe estar logueado para realizar esa accion"));
        }
        else
        {
            if(!isset($materia))
            {
                $response->getBody()->write(json_encode("No existe una materia con ese id"));
            }
            else
            {
                $datos=Alumnos_Materias:: join('usuarios', 'usuarios.id', '=', 'alumnos_materias.idAlumno')
                ->join('materias', 'materias.id', '=', 'alumnos_materias.idMateria')
                ->select('materias.nombre as Nombre de la Materia','usuarios.nombre as Nombre', 'alumnos_materias.nota as Nota')
                ->where('materias.id',$idMateria)
                ->get();

                $response->getBody()->write(json_encode($datos));
            }
        }
        

        

        return $response;
    }

    
}

<?php
namespace App\Controllers;

use App\Models\User;
use \Firebase\JWT\JWT;

class UserController
{

    public function getAll($request,$response, $args)
    {
       /* $rta= User::get();

        $response->getBody()->write(json_encode($rta));*/

        $rta = User::get();

        //var_dump($rta);

        $response->getBody()->write(json_encode($rta));

        return $response;
    
    }

    public function getOne($request,$response, $args)
    {
        
        $response->getBody()->write("getOne");

        return $response;
    
    }

    public function add($request,$response, $args)
    {

        $rta="Ya existe un usuario con ese nombre y/o email. Por favor ingrese datos distintos";
        $user = new User;

        $datos=$request->getParsedBody();

        $user->email = $datos["email"];
        $user->clave = $datos["clave"];
        $user->nombre =$datos["nombre"];
        $user->tipo = $datos["tipo"];

        $registrado = User::where('email',$user->email)->orWhere('nombre',$user->nombre)->first();
        if(!strpos($user->nombre,' '))
        {
            if(strlen($user->clave)>=4)
            {
                if(isset($registrado))
                {
                    $response->getBody()->write(json_encode("Ya existe un usuario con ese nombre y/o email. Por favor ingrese datos distintos"));
                }
                else
                {
                    $user->save();           
                    $response->getBody()->write(json_encode("Usuario guardado correctamente"));
                }
            }
            else
            {
                $response->getBody()->write(json_encode("La clave debe tener al menos 4 caracteres"));
            }
        }
        else
        {
            $response->getBody()->write(json_encode("El nombre no debe contener espacios. Ingrese los datos nuevamente"));
        } 

        return $response;


    
    }

    public function update($request,$response, $args)
    {
        $id = $args['id'];

       

        $user = User::find($id);


        if($user != null)
        {
            $user->Nombre= "Chiche";

            $rta= $user->save();
            $response->getBody()->write(json_encode($rta));
        }
        else
        {
            $response->getBody()->write("El usuario no existe");
        }
        
        return $response;
    
    }

    public function delete($request,$response, $args)
    {
        
        $response->getBody()->write("Delete");

        $parsedBody = $request->getParsedBody()['nombre'];

        

        echo $parsedBody;

        return $response;
    
    }

    public function register($request, $response, $args)
    {
        $response->getBody()->write("Registro");
        

        return $response;
    }

    public function login($request, $response, $args)
    {
        $datos = $request->getParsedBody();

        $token=UserController::BuscarUsuario($datos['clave'], $datos['email']??"", $datos['nombre'] ?? "");

        if($token !=false)
        {
            $response->getBody()->write(json_encode($token));
            
        }
        else
        {
            $response->getBody()->write(json_encode("Usuario no encontrado"));
        }
            
            return $response;
        
    }


    static function BuscarUsuario($clave, $email="", $nombre="")
     {           
        $payload=array();

        $esCorrecto=false;

        $user = User::where('clave',$clave)->where('email',$email)->orWhere('nombre', $nombre)->first();

                if($user != null)
                {
                   

                    $payload=array(
                    
                        "id"=>$user->id,
                        "clave"=>$clave,
                        "email"=>$email,
                        "tipo"=>$user->tipo                   

                    );
                    
                    $esCorrecto=JWT::encode($payload,'primerparcial');                    
                }
      


        return $esCorrecto;
        
     }

     public static function PermitirPermiso($token, $tipo)
        {
            $retorno = false;
            try 
            {
                $payload = JWT::decode($token, "primerparcial", array('HS256'));
               
                foreach ($payload as $value) 
                {
                    if ($value == $tipo) 
                    {

                        $retorno = true;
                    }
                }
            }catch (\Throwable $th)
                {
                    echo 'Excepcion:' . $th->getMessage();
                }
                return $retorno;
        }

    public static function ObtenerIdToken($token)
    {
        try 
        {
            $payload = JWT::decode($token, "primerparcial", array('HS256'));
            
            foreach ($payload as $key => $value) 
            {
                if($key == 'id')
                {
                    return $value;
                }
            }
        } catch (\Throwable $th)
        {
            echo 'Excepcion:' . $th->getMessage();
        }
    }


   

}
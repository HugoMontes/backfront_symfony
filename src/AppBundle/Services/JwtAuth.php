<?php
namespace AppBundle\Services;
// Importar JWT para crear el token
use Firebase\JWT\JWT;
class JwtAuth{

  public $manager;
  // Crear nuevo atributo que contiene una clave secreta para codificar y de codificar el token
  public $key;

  public function __construct($manager){
    $this->manager=$manager;
    // Inicializar la clase secreta
    $this->key='s3cr3t';
  }
  // $getHash: indica si se quiere codificar o decodificar el token
  public function signup($email, $password, $getHash=null){
    // Realizar la consulta que retorne un usuario con un determinado email y password
    $user=$this->manager->getRepository('BackendBundle:User')->findOneBy(array('email'=>$email,'password'=>$password));
    // Verificar la consulta
    $signup=false;
    if(is_object($user)){
      $signup=true;
    }
    if($signup){
      // AQUI SE TIENE QUE GENERAR TOKEN JWT
      // Codificar la informacion mediante un token
      $token=array(
        'sub'=>$user->getId(),
        'email'=>$user->getEmail(),
        'name'=>$user->getName(),
        'surname'=>$user->getSurname(),
        'iat'=>time(),
        // El token expira en una semana
        'exp'=>time()+(7*24*60*60),
      );
      // Empaquetar con encode(token, clave, algoritmo_codigicacion)
      $jwt=JWT::encode($token, $this->key, 'HS256');
      // Verificar si se quiere codificar o de codificar un token
      if($getHash==null){
        $data=$jwt;
      }else{
        // Decodificar informacion del token
        $decoded=JWT::decode($jwt, $this->key, array('HS256'));
        $data=$decoded;
      }
      /*
      $data=array(
        'status'=>'success',
        'user'=>$user
      );
      */
    }else{
      $data=array(
        'status'=>'error',
        'data'=>'Login incorrecto!'
      );
    }
    return $data;
  }

  public function checkToken($jwt, $getIdentity=false){
    $auth=false;
    try{
      $decoded=JWT::decode($jwt, $this->key, array('HS256'));
      if(isset($decoded) && is_object($decoded) && isset($decoded->sub)){
        $auth=true;
      }
    }catch(\UnexpectedValueException $e){
      $auth=false;
    }catch(\DomainException $e){
      $auth=false;
    }
    if($getIdentity){
      return $decoded;
    }else{
      return $auth;
    }
  }
}

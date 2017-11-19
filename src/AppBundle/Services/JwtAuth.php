<?php
namespace AppBundle\Services;
// Importar JWT
use Firebase\JWT\JWT;
class JwtAuth{

  public $manager;

  public function __construct($manager){
    $this->manager=$manager;
  }

  public function signup($email, $password){
    // Realizar la consulta que retorne un usuario con un determinado email y password
    $user=$this->manager->getRepository('BackendBundle:User')->findOneBy(array('email'=>$email,'password'=>$password));
    // Verificar la consulta
    $signup=false;
    if(is_object($user)){
      $signup=true;
    }
    if($signup){
      // AQUI SE TIENE QUE GENERAR TOKEN JWT
      $data=array(
        'status'=>'success',
        'user'=>$user
      );
    }else{
      $data=array(
        'status'=>'error',
        'data'=>'Login incorrecto!'
      );
    }
    return $data;
  }
}

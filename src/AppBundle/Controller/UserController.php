<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

// Importar la entidad User
use BackendBundle\Entity\User;
// Importar libreria de validaciones de symfony
use Symfony\Component\Validator\Constraints as Assert;
// Importar Helper
use AppBundle\Services\Helper;

class UserController extends Controller{
  public function newAction(Request $request){
      // echo 'Hola UserContoller';
      // die();
      // Cargar el servicio helper
      $helper=$this->get(Helper::class);
      // Recibir los datos que llegan por post
      $json=$request->get("json", null);
      // print_r($json);
      // die();
      // Decodificando json a objecto PHP
      $params=json_decode($json);
      // print_r($params);
      // die();
      // Validar los datos
      $data=array(
        'status'=>'error',
        'code'=>500,
        'msg'=>'Usuario no creado!!',
      );
      if($json!=null){
        // Recuperando datos del objeto $params
        $name=isset($params->name)?$params->name:null;
        $surname=isset($params->surname)?$params->surname:null;
        $email=isset($params->email)?$params->email:null;
        $password=isset($params->password)?$params->password:null;
        $createdAt=new \Datetime("now");
        $role='user';
        // Validando email
        $emailConstraint=new Assert\Email();
        $emailConstraint->message='Este email no es valido';
        $validate_email=$this->get('validator')->validate($email, $emailConstraint);
        // Validando datos, exigiendo datos obligatorios
        if($email!=null && count($validate_email)==0 && $password!=null && $name!=null && $surname!=null){
          // Crear objeto usuario
          $user=new User();
          $user->setName($name);
          $user->setSurname($surname);
          $user->setEmail($email);
          $user->setPassword($password);
          $user->setRole($role);
          $user->setCreatedAt($createdAt);
          // Guardar en la base de datos
          $em=$this->getDoctrine()->getManager();
          // Realizando consulta, tratando de obtener usuario duplicado
          // en funcion a su email.
          $isset_user=$em->getRepository('BackendBundle:User')->findBy(array(
            'email'=>$email
          ));
          // Verificar si existe el usuario
          if(count($isset_user)==0){
            $em->persist($user);
            $em->flush();
            $data=array(
              'status'=>'success',
              'code'=>200,
              'msg'=>'Usuario creado!!',
              'user'=>$user
            );
          }else{
            $data=array(
              'status'=>'error',
              'code'=>500,
              'msg'=>'Usuario no creado, duplicado!!',
            );
          }
        }
      }
      return $helper->json($data);
  }
}

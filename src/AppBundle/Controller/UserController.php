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
// Importar JwtAuth
use AppBundle\Services\JwtAuth;

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
          $user->setRole($role);
          $user->setCreatedAt($createdAt);
          // Cifrar la password con la funcion hash propia de php
          $pwd=hash('SHA256', $password);
          $user->setPassword($pwd);
          // Entity manager para las consultas a la BD
          $em=$this->getDoctrine()->getManager();
          // Realizando consulta, tratando de obtener usuario duplicado
          // en funcion a su email.
          $isset_user=$em->getRepository('BackendBundle:User')->findBy(array(
            'email'=>$email
          ));
          // Verificar si existe un usuario duplicado
          if(count($isset_user)==0){
            // Guardar en la base de datos
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

  // Editar datos del usuario logeado
  public function editAction(Request $request){
      // Cargar el servicio helper
      $helper=$this->get(Helper::class);
      // Llamar al servicio de autenticacion JwtAuth
      $jwt_auth=$this->get(JwtAuth::class);
      // Conserguir el token desde la request
      $token=$request->get('authorization',null);
      // Verificar que el token sea correcto
      $authCheck=$jwt_auth->checkToken($token);
      if($authCheck){
        // Entity manager para las consultas a la BD
        $em=$this->getDoctrine()->getManager();
        // Obtener los datos del usuario logeado dentro del token
        $identity=$jwt_auth->checkToken($token, true);
        // Obtener objeto a actualizar
        $user=$em->getRepository('BackendBundle:User')->findOneBy(array('id'=>$identity->sub));
        // Obtener datos que llegan por post
        $json=$request->get("json", null);
        // Decodificar json a objecto PHP
        $params=json_decode($json);
        // Array por defecto a devolver
        $data=array(
          'status'=>'error',
          'code'=>500,
          'msg'=>'Usuario no actualizado!!',
        );
        // Verificar que existe un json
        if($json!=null){
          // Recuperando datos del objeto $params
          $name=isset($params->name)?$params->name:null;
          $surname=isset($params->surname)?$params->surname:null;
          $email=isset($params->email)?$params->email:null;
          $password=isset($params->password)?$params->password:null;
          $updatedAt=new \Datetime("now");
          // Validando email
          $emailConstraint=new Assert\Email();
          $emailConstraint->message='Este email no es valido';
          $validate_email=$this->get('validator')->validate($email, $emailConstraint);
          // Validando datos, exigiendo datos obligatorios
          if($email!=null && count($validate_email)==0 && $name!=null && $surname!=null){
            // Setear valores a actualizar
            $user->setName($name);
            $user->setSurname($surname);
            $user->setEmail($email);
            $user->setUpdatedAt($updatedAt);
            // Verificar si password es distinto de null
            if($password!=null){
              // Cifrar la password con la funcion hash propia de php
              $pwd=hash('SHA256', $password);
              $user->setPassword($pwd);  
            }
            // Actualizar usuario en la base de datos
            $em->persist($user);
            $em->flush();
            $data=array(
                'status'=>'success',
                'code'=>200,
                'msg'=>'Usuario actualizado!!',
                'user'=>$user
            );
          }
        }
      }else{
        $data=array(
          'status'=>'error',
          'code'=>500,
          'msg'=>'Autorizacion no valida!!',
        );
      }
      return $helper->json($data);
  }
}

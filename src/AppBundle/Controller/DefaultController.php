<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

// Adicionar libreria para respuestas Json
// use Symfony\Component\HttpFoundation\JsonResponse;
// Importar Helper
use AppBundle\Services\Helper;

// Importar libreria de validaciones de symfony
use Symfony\Component\Validator\Constraints as Assert;

// Importar JwtAuth
use AppBundle\Services\JwtAuth;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    public function pruebasAction(Request $request){
        // echo 'Hola Mundo';
        $em=$this->getDoctrine()->getManager();
        $userRepo=$em->getRepository('BackendBundle:User');
        $users=$userRepo->findAll();
        // var_dump($users);
        // die();

        /*
        return new JsonResponse(array(
          'status'=>'success',
          'users'=>$users[0]->getName() // JsonResponse tiene problemas al convertir objetos php a json
        ));
        */

        // Llamando al contenedor de servicios
        /*
        $helper=$this->get(Helper::class);
        echo $helper->holaMundo();
        die();
        */

        $helper=$this->get(Helper::class);
        echo $helper->json($users[0]);
        die();

        // Haciendo uso del metodo json de symfony 3
        return $this->json(array(
          'status'=>'success',
          'users'=>$users[0]->getName(),
        ));
    }


    public function pruebasJsonAction(Request $request){
      $em=$this->getDoctrine()->getManager();
      $userRepo=$em->getRepository('BackendBundle:User');
      $users=$userRepo->findAll();

      // Obtener una instancia de Helper
      $helper=$this->get(Helper::class);
      // Devolviendo un dato
      // echo $helper->json($users[0]->getName());
      // die();
      // Devolviendo un objeto
      // echo $helper->json($users[0]);
      // die();
      // Devolviendo un array
      // return $helper->json($users);
      return $helper->json(array(
          'status'=>'success',
          'users'=>$users
      ));
    }

    public function loginAction(Request $request){
      // echo 'hola login';
      // die();
      // Cargar el servicio de Helper
      $helper=$this->get(Helper::class);
      // Recibir json por POST
      // get('nomVar', valDefaul)
      $json=$request->get('json',null);
      // Array a devolver por defecto
      $data=array(
        'status'=>'error',
        'data'=>'Favor enviar json via post!!'
      );
      if($json!=null){
        // Decodificar los datos que llegan en el json
        // json_decode: convierte un json a un objecto de php
        $params=json_decode($json);
        // Verificar que los datos no esten vacios
        // En caso de estar vacios setear null
        $email=isset($params->email)?$params->email:null;
        $password=isset($params->password)?$params->password:null;

        // Crear una instancia de validacion para email
        $emailConstraint=new Assert\Email();
        // Asignar un mensaje de error
        $emailConstraint->message='El email no es valido!';
        // Validar el email
        $validate_email=$this->get('validator')->validate($email, $emailConstraint);

        // Verificar si email tiene errores de validacion
        // Tambien verificar que passwor sea distinto de null
        if(count($validate_email)==0 && $password!=null){
          // Cargar el servicio al controlador
          $jwt_auth=$this->get(JwtAuth::class);
          // Llamar al metodo del JwtAuth
          $signup=$jwt_auth->signup($email, $password);

          $data=array(
            'status'=>'success',
            'data'=>'Login correcto!',
            'signup'=>$signup,
          );
        }else{
          $data=array(
            'status'=>'error',
            'data'=>'Email o password incorrectos!'
          );
        }
        /*
        $data=array(
          'status'=>'success',
          'data'=>'OK'
        );
        */
      }
      return $helper->json($data);
    }
}

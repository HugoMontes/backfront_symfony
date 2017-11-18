<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

// Adicionar libreria para respuestas Json
// use Symfony\Component\HttpFoundation\JsonResponse;
// Importar Helper
use AppBundle\Services\Helper;



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
}

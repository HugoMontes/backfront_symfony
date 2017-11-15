<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

// Adicionar libreria para respuestas Json
use Symfony\Component\HttpFoundation\JsonResponse;

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
        return new JsonResponse(array(
          'status'=>'success',
          'users'=>$users[0]->getName() // JsonResponse tiene problemas al convertir objetos php a json
        ));
    }

}

<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BackendBundle\Entity\Task;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Services\Helper;
use AppBundle\Services\JwtAuth;

class TaskController extends Controller{

  public function newAction(Request $request){
    // echo "Hola TaskController";
    // die();
    // Cargar los helpers
    $helper=$this->get(Helper::class);
    $jwt_auth=$this->get(JwtAuth::class);
    // Recoger token que llega por la peticion
    $token=$request->get('authorization', null);
    // Verificar si se encuentra correctamente logueado
    $authCheck=$jwt_auth->checkToken($token);
    if($authCheck){
      // Obtener los datos del usuario logeado
      $identity=$jwt_auth->checkToken($token, true);
      // Obtener datos json
      $json=$request->get('json', null);
      if($json!=null){
        // Decodificar parametros json a un objeto php
        $params=json_decode($json);
        $createdAt=new \Datetime('now');
        $updatedAt=new \Datetime('now');
        $user_id=$identity->sub!=null?$identity->sub:null;
        $title=isset($params->title)?$params->title:null;
        $description=isset($params->description)?$params->description:null;
        $status=isset($params->status)?$params->status:null;
        if($user_id!=null && $title!=null){
          // Obtener usuario que creara la tarea
          $em=$this->getDoctrine()->getManager();
          $user=$em->getRepository('BackendBundle:User')->findOneBy(array('id'=>$user_id));
          // Crear tarea seteando los datos de la misma
          $task=new Task();
          $task->setUser($user);
          $task->setTitle($title);
          $task->setDescription($description);
          $task->setStatus($status);
          $task->setCreatedAt($createdAt);
          $task->setUpdatedAt($updatedAt);
          // Guardar en la base de datos
          $em->persist($task);
          $em->flush();
          // Devolver la informacion
          $data=array(
            'status'=>'success',
            'code'=>200,
            'data'=>$task
          );
        }else{
          $data=array(
            'status'=>'error ',
            'code'=>200,
            'msg'=>'Tarea creada, error de validacion!'
          );
        }
      }else{
        $data=array(
          'status'=>'error',
          'code'=>500,
          'msg'=>'Tarea no creada, parametros con errores!'
        );
      }
    }else{
      $data=array(
        'status'=>'error',
        'code'=>500,
        'msg'=>'Autorizacion no valida!'
      );
    }
    return $helper->json($data);
  }
}

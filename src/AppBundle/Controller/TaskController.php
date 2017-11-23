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
            'code'=>500,
            'msg'=>'Tarea no creada, error de validacion!'
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

  public function editAction(Request $request, $id=null){
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
        $updatedAt=new \Datetime('now');
        $user_id=$identity->sub!=null?$identity->sub:null;
        $title=isset($params->title)?$params->title:null;
        $description=isset($params->description)?$params->description:null;
        $status=isset($params->status)?$params->status:null;
        if($user_id!=null && $title!=null){
          // Verificar si existe id de tarea a modificar
          if($id!=null){
            // Obtener la tarea de la base de datos
            $em=$this->getDoctrine()->getManager();
            $task=$em->getRepository('BackendBundle:Task')->findOneBy(array('id'=>$id));
            // Verificar si existe la identidad del usuario logueado
            // y si el usuario es dueño de la tarea
            if(isset($identity->sub) && $identity->sub==$task->getUser()->getId()){
              // Reemplazar valores
              $task->setTitle($title);
              $task->setDescription($description);
              $task->setStatus($status);
              $task->setUpdatedAt($updatedAt);
              // Guardar cambios en la base de datos
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
                'status'=>'error',
                'code'=>500,
                'data'=>'Tarea no actualizada, usted no es dueño de la tarea.'
              );
            }
          }else{
            $data=array(
              'status'=>'error',
              'code'=>500,
              'msg'=>'Tarea no actualizada, enviar id en la peticion'
            );
          }
        }else{
          $data=array(
            'status'=>'error ',
            'code'=>500,
            'msg'=>'Tarea no actualizada, error de validacion!'
          );
        }
      }else{
        $data=array(
          'status'=>'error',
          'code'=>500,
          'msg'=>'Tarea no actualizada, parametros con errores!'
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

  // Listar las tareas unicamente del usuario logueado
  public function listAction(Request $request){
    // Cargar el servicio Helper
    $helper=$this->get(Helper::class);
    // Cargar el servicio JwtAuth para comprobar token
    $jwt_auth=$this->get(JwtAuth::class);
    // Recoger token que llega por la peticion POST
    $token=$request->get('authorization', null);
    // Verificar si se encuentra correctamente logueado
    $authCheck=$jwt_auth->checkToken($token);
    if($authCheck){
      // Obtener los datos del usuario logeado
      $identity=$jwt_auth->checkToken($token, true);
      // Realizar la consulta para listar las tareas
      $em=$this->getDoctrine()->getManager();
      $dql='SELECT t FROM BackendBundle:Task t ORDER BY t.id DESC';
      $query=$em->createQuery($dql);
      // Recoger el parametro entero de page de la url
      $page=$request->query->getInt('page',1);
      // Recoger el servicio de KnpPaginator
      $paginator=$this->get('knp_paginator');
      // Mostrar 10 tareas por pagina
      $items_per_page=10;
      // Cargar las tareas en $pagination
      $pagination=$paginator->paginate($query,$page,$items_per_page);
      // Guardar numero total de registros
      $total_items_count=$pagination->getTotalItemCount();
      // Retornar los datos en el array
      $data=array(
        'status'=>'success',
        'code'=>200,
        'total_items_count'=>$total_items_count,
        'page_actual'=>$page,
        'items_per_page'=>$items_per_page,
        // Ceil: Funcion de php para redondear
        'total_pages'=>ceil($total_items_count/$items_per_page),
        'data'=>$pagination
      );
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

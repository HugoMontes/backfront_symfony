<?php
namespace AppBundle\Services;
// Importar clase para serializar objetos php a json
use JMS\Serializer\SerializerBuilder;

class Helper{

  public $manager;

  public function __construct($manager){
    $this->manager=$manager;
  }

  public function holaMundo(){
    return "Hola mundo desde mi servicio de symfony";
  }

/*
  public function json($data){
    $serializer = SerializerBuilder::create()->build();
    $response = $serializer->serialize($data, 'json');
    return $response;
  }
*/
/*
  public function json($data){
    $normalizers = array(new \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer());
    $encoders=array('json'=>new \Symfony\Component\Serializer\Encoder\JsonEncoder());

    $serializer=new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
    $json=$serializer->serialize($data, 'json');

    $response=new \Symfony\Component\HttpFoundation\Response();
    $response->setContent($json);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
*/
/*
  public function json($data){

    $normalizers = array(new \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer());
    $encoders=array('json'=>new \Symfony\Component\Serializer\Encoder\JsonEncoder());

    $serializer=new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
    $json=$serializer->serialize($data, 'json');


    $response=new \Symfony\Component\HttpFoundation\Response();
    $response->setContent($json);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
  */

  public function json($data){
    $serializer = SerializerBuilder::create()->build();
    $json = $serializer->serialize($data, 'json');
    $response=new \Symfony\Component\HttpFoundation\Response();
    $response->setContent($json);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}

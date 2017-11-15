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

  public function json($data){
    $serializer = SerializerBuilder::create()->build();
    $response = $serializer->serialize($data, 'json');
    return $response;
  }

}

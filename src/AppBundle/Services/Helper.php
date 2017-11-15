<?php
namespace AppBundle\Services;
class Helper{

  public $manager;

  public function __construct($manager){
    $this->manager=$manager;
  }

  public function holaMundo(){
    return "Hola mundo desde mi servicio de symfony";
  }
}

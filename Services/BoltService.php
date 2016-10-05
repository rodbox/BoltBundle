<?php
namespace RB\BoltBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BoltService {

    public function __construct($container, $session, $router)
    {
        $this->container = $container;
        $this->router    = $router;
        $this->session   = $session;
    }

    // charge l'item
    function load($item ,$context)
    {
        # code...
    }

    function LoadFree (){

    }

    function LoadData (){

    }

    function LoadFunction (){

    }

    function LoadEntity (){

    }

    function LoadJson (){

    }

    function LoadRoute (){

    }

    function LoadFolder (){

    }

    function LoadService (){

    }


   
}

?>
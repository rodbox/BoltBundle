<?php
namespace RB\BoltBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BoltExtension  extends \Twig_Extension{
    public function __construct($container, $twig, $session)
    {
        $this->container = $container;
        $this->twig      = $twig;
        $this->session   = $session;
    }



    public function boltInput($name)
    {
        echo $this->twig->render('RBBoltBundle:Default:input.html.twig',['id'=>$name]);
    }






    public function getName(){
        return 'rb_bolt_extension';
    }



    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction("boltInput", [$this, 'boltInput'], ['is_safe' => ['html']])
        );
    }
}

?>
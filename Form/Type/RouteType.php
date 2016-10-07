<?php 
namespace RB\BoltBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;


class RouteType extends AbstractType  implements ContainerAwareInterface

{

    protected $container;
    protected $routes;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->routes =['klmlmklm'];
    }
   
    public function configureOptions(OptionsResolver $resolver)
    {

       // $routeCollection = $resolver->get('router');
        
        //$this->get('router')->getRouteCollection()->all();

        $routes= $this->routes;
/*

        foreach($routeCollection as $route => $routeMeta){
            $path     = $routeMeta->getPath();
            preg_match_all('[\{[{a-zA-Z0-9]{1,}\}]',$path , $matches);
            $routes[$route] = $route;
            $routes[] = [
                'name' => $route,
                'path' => $path,
                'req'  => json_encode($matches[0])
            ];
        }*/

        $resolver->setDefaults([
            'placeholder' => 'action.choose_route',
            'choices'     => $routes,
            'attr'=>[
            ]
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}

?>
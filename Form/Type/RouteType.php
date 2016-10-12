<?php 
namespace RB\BoltBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use RB\BoltBundle\Form\Type\ContainerAwareType;

class RouteType extends AbstractType  implements ContainerAwareInterface
{

    protected $container;
    protected $routes;


    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
   


    public function configureOptions(OptionsResolver $resolver)
    {

        $routes= $this->routes;
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
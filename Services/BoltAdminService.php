<?php
namespace RB\BoltBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\AbstractType;


class BoltAdminService {

    public function __construct($container, $session, $router, $doctrine)
    {
        $this->container = $container;
        $this->router    = $router;
        $this->session   = $session;
        $this->doctrine  = $doctrine;
    }

    // charge l'item
    function export($bolt ,$project)
    {
        # code...
    }


    public function routes()
    {
        $routeCollection = $this->router->getRouteCollection()->all();

        foreach($routeCollection as $route => $routeMeta){
            $path     = $routeMeta->getPath();
            preg_match_all('[\{[{a-zA-Z0-9]{1,}\}]', $path , $matches);
            $routes[] = [
                'name'   => $route,
                'path'   => $path,
                'req'    => json_encode($matches[0]),
                'reqStr' => implode(',', $matches[0])
            ];
        }

        return $routeCollection;
    }

    public function services()
    {
        $serviceCollection = $this->container->getServiceIds();
        $services = [];
         foreach ($serviceCollection as $key => $value)
            $services[$value]=$value;

        return $services;
    }


    public function init()
    {
        return [
            'codemirror' => 'codemirror',
            'paperjs'    => 'paperjs',
            'plupload'   => 'plupload',
            'summernote' => 'summernote',
            'crop'       => 'crop'
        ];
    }


    public function folder()
    {
        return [
            'dir_cdn'     => 'dir_cdn',
            'dir_user'    => 'dir_user',
            'dir_project' => 'dir_project',
            'dir_tmp'     => 'dir_tmp'
        ];
    }


    public function entitys()
    {

        $em = $this->doctrine->getManager();
        $classes = [];
        $metas  = $em->getMetadataFactory()->getAllMetadata();
        foreach ($metas as $meta)
            $classes[$meta->getName()] = $meta->getName();

        return $classes;
    }



    public function forms()
    {

        $forms = [];
        /* foreach ($formCollection->getExtensions()  as $key => $value)
            $forms[$value]=$value;
*/
        return $forms;
    }


    public function bundles()
    {
        $bundlesKernel = $this->container->getParameter('kernel.bundles');

        foreach ($bundlesKernel as $key => $value)
            $bundles[] = $key;

        return $bundlesKernel;
    }


    public function roles()
    {
        $allRoles = $this->container->getParameter('security.role_hierarchy.roles');
        /*$swith                          = array_pop($allRoles["ROLE_SUPER_ADMIN"]);
        $allRoles["ROLE_SUPER_ADMIN"][] = "ROLE_SUPER_ADMIN";*/

        foreach ($allRoles as $key => $value)
            $roles[$key] = $key;

        return $roles;
    }


    public function projects()
    {
        $em = $this->doctrine->getManager();
        $projectsData = $em
          ->getRepository('RBBoltBundle:Projects')
          ->findAll();

        $projects = [];
        foreach ($projectsData as $key => $value) {
            $name = $value->getName();
            $projects[$name]=$name;
        }

        return $projects;
    }
}

?>
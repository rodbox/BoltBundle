<?php
namespace RB\BoltBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use RB\BoltBundle\Entity\Type;
use RB\BoltBundle\Entity\Projects;
use RB\BoltBundle\Entity\Item;

class LoadType implements FixtureInterface
{

  public function load(ObjectManager $em)
  {
    $types = [
      [
          'name'        => 'free',
          'description' => 'description.free',
          'color'       => '#F76C51'
      ], 
      [
          'name'        => 'data',
          'description' => 'description.data',
          'color'       => '#4EBCDA'
      ],
      [
          'name'        => 'function',
          'description' => 'description.function',
          'color'       => '#27292C'
      ],
      [
          'name'        => 'entity',
          'description' => 'description.entity',
          'color'       => '#009E4D'
      ],
      [
          'name'        => 'json',
          'description' => 'description.json',
          'color'       => '#98BC73'
      ],
      [
          'name'        => 'route',
          'description' => 'description.route',
          'color'       => '#878787'
      ],
      [
          'name'        => 'folder',
          'description' => 'description.folder',
          'color'       => '#949597'
      ],
      [
          'name'        => 'service',
          'description' => 'description.service',
          'color'       => '#DC3522'
      ]
    ];


    $projects = [
      [
        'name'        => 'default',
        'description' => 'projet par default',
        'pointers'    => [],
        'security'    => 'FREE',
        'do'          => 'alert("default")'
      ]
    ];


    


    $itemsObject    = [];
    $projectsObject = [];
    $typesObject    = [];

    foreach ($types as $key_img => $typeInfo) {
      $type = new Type();
      $type->setName($typeInfo['name']);
      $type->setDescription($typeInfo['description']);
      $type->setColor($typeInfo['color']);

      $em->persist($type);

      $typesObject[$typeInfo['name']] = $type;
    }

    foreach ($projects as $key_img => $projectInfo) {
      $project = new projects();
      $project->setName($projectInfo['name']);
      $project->setDescription($projectInfo['description']);
      $project->setSecurity($projectInfo['security']);
      $project->setPointers($projectInfo['pointers']);
      $project->setDo($projectInfo['do']);

      $em->persist($project);
      
      $projectsObject[$projectInfo['name']] = $project;
    }

    $items = [
      [
        'name'        => 'free',
        'type'        => $typesObject['free'],
        'description' => 'champ libre absolue',
        'context'     => 'default',
        'view'        => 'false',
        'multiple'    => false,
        'projects'    => [$projectsObject['default']]
      ],
      [
        'name'        => 'user',
        'type'        => $typesObject['entity'],
        'description' => 'la liste des utilisateurs',
        'context'     => 'default',
        'view'        => 'default',
        'multiple'    => false,
        'projects'    => [$projectsObject['default']]
      ],
      [
        'name'        => 'json',
        'type'        => $typesObject['json'],
        'description' => 'un fichier json ou une url api rest',
        'context'     => 'default',
        'view'        => 'default',
        'multiple'    => false,
        'projects'    => [$projectsObject['default']]
      ],
      [
        'name'        => 'folder',
        'type'        => $typesObject['folder'],
        'description' => 'un fichier folder ou une url api rest',
        'context'     => 'default',
        'view'        => 'default',
        'multiple'    => false,
        'projects'    => [$projectsObject['default']]
      ],
      [
        'name'        => 'Alert',
        'type'        => $typesObject['function'],
        'description' => 'une fonction alert',
        'context'     => 'default',
        'view'        => 'default',
        'multiple'    => false,
        'projects'    => [$projectsObject['default']]
      ]
    ];

    foreach ($items as $key_img => $itemInfo) {
      $item = new item();
      $item->setName($itemInfo['name']);
      $item->setDescription($itemInfo['description']);
      $item->setContext($itemInfo['context']);
      $item->setType($itemInfo['type']);
      $item->setView($itemInfo['view']);
      $item->setMultiple($itemInfo['multiple']);
      $item->setProjects($itemInfo['projects']);

      $em->persist($item);
      
      $itemsObject[$itemInfo['name']] = $project;
    }


    $em->flush();
  }

}
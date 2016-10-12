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
      ],
      [
          'name'        => 'group',
          'description' => 'description.group',
          'color'       => '#ffef00'
      ],
      [
          'name'        => 'init',
          'description' => 'description.init',
          'color'       => '#4b1194'
      ],
      [
          'name'        => 'bolt_project',
          'description' => 'description.bolt_project',
          'color'       => '#00ca2a'
      ]
    ];


    $projects = [
      [
        'name'        => 'default',
        'description' => 'projet par default',
        'pointers'    => [],
        'security'    => ['ROLE_USER'],
        'do'          => 'alert("default")',
        'data'        => [
            'bolt'=>[
                'start'=>[],
                'item'=>[]
            ]
        ]
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
        'lockme'        => false,
        'projects'    => [$projectsObject['default']],
        'meta'        => [
          'ref'=>'\\*\\'
        ]
      ],
      [
        'name'        => 'user',
        'type'        => $typesObject['entity'],
        'description' => 'la liste des utilisateurs',
        'context'     => 'default',
        'view'        => 'default',
        'multiple'    => false,
        'lockme'        => true,
        'projects'    => [$projectsObject['default']],
        'meta'        => [
          'entity'=>''
        ]
      ],
      [
        'name'        => 'json',
        'type'        => $typesObject['json'],
        'description' => 'un fichier json ou une url api rest',
        'context'     => 'default',
        'view'        => 'default',
        'multiple'    => false,
        'lockme'    => false,
        'projects'    => [$projectsObject['default']],
        'meta'        => []
      ],
      [
        'name'        => 'ZIP',
        'type'        => $typesObject['service'],
        'description' => 'compresse un fichier ou un dossier et retourne le chemin',
        'context'     => 'default',
        'view'        => 'default',
        'multiple'    => false,
        'lockme'        => true,
        'projects'    => [$projectsObject['default']],
        'meta'        => []
      ],
      [
        'name'        => 'folder',
        'type'        => $typesObject['folder'],
        'description' => 'un fichier folder ou une url api rest',
        'context'     => 'default',
        'view'        => 'default',
        'multiple'    => false,
        'lockme'    => false,
        'projects'    => [$projectsObject['default']],
        'meta'        => []
      ],
      [
        'name'        => 'alert',
        'type'        => $typesObject['function'],
        'description' => 'une fonction alert',
        'context'     => 'default',
        'view'        => 'default',
        'multiple'    => false,
        'lockme'    => false,
        'projects'    => [$projectsObject['default']],
        'meta'        => [
          'function'=>'alert("toto de alert")'
        ]
      ],
      [
        'name'        => 'Send',
        'type'        => $typesObject['function'],
        'description' => 'send mail',
        'context'     => 'default',
        'view'        => 'default',
        'multiple'    => false,
        'lockme'    => false,
        'projects'    => [$projectsObject['default']],
        'meta'        => [
          'function'=>'alert("send mail")'
        ]
      ],
      [
        'name'        => 'Init',
        'type'        => $typesObject['init'],
        'description' => 'une initialisation',
        'context'     => 'default',
        'view'        => 'default',
        'multiple'    => false,
        'lockme'    => false,
        'projects'    => [$projectsObject['default']],
        'meta'        => []
      ],
      [
        'name'        => 'group',
        'type'        => $typesObject['group'],
        'description' => 'un groupe',
        'context'     => 'default',
        'view'        => 'default',
        'multiple'    => false,
        'lockme'    => false,
        'projects'    => [$projectsObject['default']],
        'meta'        => []
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
      $item->setLockme($itemInfo['lockme']);
      $item->setMeta($itemInfo['meta']);
      $item->setProjects($itemInfo['projects']);

      $em->persist($item);
      
      $itemsObject[$itemInfo['name']] = $project;
    }


    $em->flush();
  }

}
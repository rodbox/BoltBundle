<?php

namespace RB\BoltBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use RB\BoltBundle\Form\ProjectsType;
use RB\BoltBundle\Form\ItemType;
/**
* @Route("/bolt")
*/
class DefaultController extends Controller
{
    /**
     * @Route("/",name="bolt")
     */
    public function indexAction()
    {
        $formItem       = $this->createForm('RB\BoltBundle\Form\ItemProjectsType');
        $formProjects   = $this->createForm('RB\BoltBundle\Form\ProjectsType');
        
        return $this->render('RBBoltBundle:Default:index.html.twig',[
            'formItem'      => $formItem->createView(),
            'formProjects'  => $formProjects->createView()
        ]);
    }

	/**
	* @Route("/player",name="bolt_player")
	*/
	public function bolt_playerAction(Request $request)
    {
       	$list = [];

        $r    = [
    	    'infotype' => 'success',
            'msg'      => 'action : ok',
    	    'app'      => $this->renderView('::base.html.twig', [
            	'list' => $list
    	    ])
        ];

    	return new JsonResponse($r);
    }


    /**
    * @Route("/bolt_get_info/{type}",name="bolt_get_info", options={"expose"=true}, defaults={"type"="route"})
    */
    public function bolt_get_infoAction(Request $request, $type)
    {
       $list = [];

        $r    = [
            'infotype' => 'success',
            'msg'      => 'action : ok',
            'app'      => $this->renderView('::base.html.twig', [
            'list' => $list
            ])
        ];

        return new JsonResponse($r);
    }

        	
}

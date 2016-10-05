<?php

namespace RB\BoltBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
        return $this->render('RBBoltBundle:Default:index.html.twig');
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
        	
}

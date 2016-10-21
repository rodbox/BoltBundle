<?php

namespace RB\BoltBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use RB\BoltBundle\Form\ProjectsType;
use RB\BoltBundle\Form\ItemType;

use RB\BoltBundle\Entity\Projects;
use RB\BoltBundle\Entity\Item;
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

        /* SERVICE : router */
        $bolt = $this->get('rb_bolt.admin.services');
        /* END SERVICE :  router */

        $data = [
            'bolt'        => $bolt
        ];

        $formItem       = $this->createForm('RB\BoltBundle\Form\ItemProjectsType', null, $data);
        $formProjects   = $this->createForm('RB\BoltBundle\Form\ProjectsType', null, $data);
        
        return $this->render('RBBoltBundle:Default:index.html.twig',[
            'formItem'      => $formItem->createView(),
            'formProjects'  => $formProjects->createView()
        ]);
    }

	/**
	* @Route("/pl",name="bolt_player")
	*/
	public function bolt_playerAction(Request $request)
    {
        $name     = $request->query->get("name");
        $em       = $this->getDoctrine()->getManager();
        $projects = $em
            ->getRepository('RBBoltBundle:Projects')
            ->findOneByName($name);

        /* SERVICE : rb.serializer */
        $project = $this->get('rb.serializer')->normalize($projects);
        /* END SERVICE :  rb.serializer */

        $r = [
            'infotype' => 'success',
            'msg'      => 'ok',
            'bolt'     => $project
        ];
        
        return new JsonResponse($project['meta']);
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

<?php

namespace RB\BoltBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
* @Route("/admin/bolt",name="bolt")
*/
class AdminBoltController extends Controller
{
	/**
	* @Route("/load",name="bolt_load")
	*/
	public function loadAction(Request $request)
    {
       	$bolt = $request->request->get('bolt',[]);
        $project = $request->request->get('project',[]);


        $em      = $this->getDoctrine()->getManager();
        $projects = $em
          ->getRepository('RBBoltBundle:Projects')
          ->findOneByName($project['name']);

        /* SERVICE : rb.serializer */
        $project = $this->get('rb.serializer')->normalize($projects);
        /* END SERVICE :  rb.serializer */


    	return new JsonResponse($project);
	}


	/**
	* @Route("/save",name="bolt_save")
	*/
	public function saveAction(Request $request)
    {
        
        $bolt = $request->request->get('bolt',[]);
        $project = $request->request->get('project',[]);


        $em      = $this->getDoctrine()->getManager();
        $project = $em
          ->getRepository('RBBoltBundle:Projects')
          ->findOneByName($project['name']);

        $project->setBolt($bolt);
        $em->persist($project);
        $em->flush();
        
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

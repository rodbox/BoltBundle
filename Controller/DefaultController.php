<?php

namespace RB\BoltBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Session\Session;

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
	* @Route("/pl",name="bolt_player" , options={"expose"=true})
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

        $starts  = array_map(function($value) {
            $refExplode = explode('-',substr($value,1));
            return $refExplode[0];
        },$project['meta']['start']);


        $where = 'item.id IN(:ids) ';
        $qb = $em
            ->createQueryBuilder('item')
            ->select('item')
            ->from('RBBoltBundle:Item','item')
            ->leftJoin('item.type', 'type')
            ->where($where)
            ->setParameter('ids', $starts)
            ->getQuery()
            ->getResult();

        $results = $this->get('rb.serializer')->normalize($qb);
/*
$results = $em
            ->getRepository('RBBoltBundle:Item')
            ->findAll();
        $results = $this->get('rb.serializer')->normalize($results);*/
        $session = $request->getSession();

        // set and get session attributes
        $session->set('bolt', $project);

        $r = [
            'infotype' => 'success',
            'msg'      => 'ok',
            'start'    => $results,
            'bolt'     => $project,
            'data'     => $results,
            'view'     => '<a href="#" class="list-group-item" @click="initRoad" data-road="road">{{name}}</a>',
        ];
        
        return new JsonResponse($r);
    }


    /**
    * @Route("/bitem",name="bolt_item", options={"expose"=true})
    */
    public function loadItemAction(Request $request)
    {
        /* SERVICE : rb.bolt */
        $data = $this->get('rb_bolt.services')->load($request);
        /* END SERVICE :  rb.bolt */

        return new JsonResponse($data);
    }



    /**
    * @Route("/rexec",name="bolt_road_init", options={"expose"=true})
    */
    public function roadInitAction(Request $request)
    {
        /* SERVICE : rb.bolt */
        $data = $this->get('rb_bolt.services')->init();
        /* END SERVICE :  rb.bolt */

        return new JsonResponse($data);
    }



    /**
    * @Route("/rexec",name="bolt_road_execute", options={"expose"=true})
    */
    public function roadExecuteAction(Request $request)
    {
        /* SERVICE : rb.bolt */
        $data = $this->get('rb_bolt.services')->execute();
        /* END SERVICE :  rb.bolt */

        return new JsonResponse($data);
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

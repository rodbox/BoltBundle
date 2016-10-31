<?php

namespace RB\BoltBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Filesystem\Filesystem;
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
        $bolt     = $request->request->get('bolt',[]);
        $project  = $request->request->get('project',[]);

        $em       = $this->getDoctrine()->getManager();
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
        $bolt     = $request->request->get('bolt',[]);
        $project  = $request->request->get('project',[]);
        $meta     = $request->request->get('meta',[]);

        $dir_bolt = $this->container->getParameter('dir_bolt');

        /* SERVICE : rb_file */
        //file_put_contents($dir_bolt.'/hy.json', json_encode($hydrate,JSON_PRETTY_PRINT));
        file_put_contents($dir_bolt.'/bolt-'.$meta['name'].'-expose.json', json_encode($meta,JSON_PRETTY_PRINT));
        file_put_contents($dir_bolt.'/bolt-'.$meta['name'].'-expose-min.json', json_encode($meta));
        //$toJson = $this->get('rb_file')->toJson($hydrate,$dir_bolt.'/hy.json');
        /* END SERVICE :  rb_file */

        $em       = $this->getDoctrine()->getManager();
        $project  = $em
          ->getRepository('RBBoltBundle:Projects')
          ->findOneByName($project['name']);

        $project->setBolt($bolt);
        $project->setMeta($meta);
        $em->persist($project);
        $em->flush();

        /* SERVICE : rb_bolt.admin.service */
        $export = $this->get('rb_bolt.admin.services')->export($bolt,$project);
        /* END SERVICE :  rb_bolt.admin.service */
        
        $list = [];

        $r    = [
            'infotype' => 'success',
            'meta'     => $meta
        ];

        return new JsonResponse($r);
    }


    /**
    * @Route("/entity_meta",name="entity_meta" , options={"expose"=true})
    */
    public function entity_metaAction(Request $request)
    {

        $entity = $request->query->get("entity");
        /* SERVICE : rb_bolt_admin */
        $entityMeta = $this->get('rb_bolt.admin.services')->entityMeta($entity);
        /* END SERVICE :  rb_bolt_admin */

        $r    = [
            'infotype' => 'success',
            'msg'      => 'action : ok',
            'list'      => $entityMeta
        ];

        return new JsonResponse($r);
    }        
}

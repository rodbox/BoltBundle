<?php

namespace RB\BoltBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use RB\BoltBundle\Entity\Item;
use RB\BoltBundle\Form\ItemType;

/**
 * Item controller.
 *
 * @Route("/item")
 */
class ItemController extends Controller
{
    
      /**
      * @Route("/upd_by_project",name="upd_by_project")
      */
      public function upd_by_projectAction(Request $request)
      {
        $item_projects = $request->request->get("item_projects");
        $id = $request->request->get("id");
        extract($item_projects["base"]);
        extract($item_projects["all"]);

        //$meta          = $item_projects["meta"][$type];
        $em            = $this->getDoctrine()->getManager();


        $type = $em
          ->getRepository('RBBoltBundle:Type')
          ->find($type);
        
        $meta          = (isset($item_projects["meta"][$type->getName()]))?$item_projects["meta"][$type->getName()]:[];

        $item          = $em
          ->getRepository('RBBoltBundle:Item')
          ->find($id);
        
        $item->setMeta($meta);
        $item->setType($type);
        $item->setView($view);
        $item->setAutomatic(isset($automatic)); 
        $item->setLockme(isset($lockme)); 
        $item->setMultiple(isset($multiple)); 

        $em->persist($item);

        $em->flush();

        $r    = [
            'infotype' => 'success',
            'msg'      => 'Item e'.$item->getId().' enregistré',
            'app'      => $name
        ];

        return new JsonResponse($r);
      }

    /**
    * @Route("/load/{name}",name="bolt_item_load", options={"expose"=true})
    */
    public function loadAction(Request $request, $name)
    {
        
        $em       = $this->getDoctrine()->getManager();
        $session  = $request->getSession();

        $entities = $em
            ->getRepository('RBBoltBundle:Item')
            ->findOneByName($name);

        $session = $request->getSession();
        // set and get session attributes
        $project = $session->get('bolt');

        /* SERVICE : rb.serializer */
        $list = $this->get('rb.serializer')->normalize($entities);
        /* END SERVICE :  rb.serializer */

        return new JsonResponse($list);
    }

    /**
    * @Route("/expose",name="bolt_item_expose")
    */
    public function exposeAction(Request $request)
    {
        
        $em       = $this->getDoctrine()->getManager();
        $session  = $request->getSession();

        $filter   = $request->query->get("filter",'all');

        $entities = $em
          ->getRepository('RBBoltBundle:Item');

        if ($filter == 'all'){

            $entities = $em
                ->getRepository('RBBoltBundle:Item')
                ->findBy([],['type'=>'ASC']);

        }

        else {
            $entities = $em
                ->getRepository('RBBoltBundle:Item')
                ->findAll();
            // construct filter sys
        }

        /* SERVICE : rb.serializer */
        $list = $this->get('rb.serializer')->normalize($entities);
        /* END SERVICE :  rb.serializer */

        return new JsonResponse($list);
    }



    /**
     * Lists all Item entities.
     *
     * @Route("/", name="bolt_item_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em      = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $ui      = $session->get('ui');
        $view    = (isset($ui['V'])) ?$ui['V']:'index-A';
        $per     = (isset($ui['per'])) ?$ui['per']:25;

        $query   = $em->createQuery('SELECT p FROM RBBoltBundle:Item p');
        $paginator = $this->get('knp_paginator');
        $items  = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $per
        );


        return $this->render('RBBoltBundle:Default:index.html.twig', [
            'items' => $items,
        ]);
    }



    /**
     * Creates a new Item entity.
     *
     * @Route("/new", name="bolt_item_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $item = new Item();
        $form = $this->createForm('RB\BoltBundle\Form\ItemType', $item);

        $item->setLockme(true);
        $item->setAutomatic(false);
        $item->setDefaultValue('');
        $item->setMeta([]);
        $form->handleRequest($request);


        if ($request->isXmlHttpRequest() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            $r = [
                'infotype' => 'success',
                'msg'      => 'ok',
                'autoclose'=> true,
                'click'    => ['#bolt_items_load']
            ];

            return new JsonResponse($r);
        }

        return $this->render('RBBoltBundle:item:new.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }



    /**
     * Finds and displays a Item entity.
     *
     * @Route("/{id}", name="bolt_item_show")
     * @Method("GET")
     */
    public function showAction(Item $item)
    {
        $deleteForm = $this->createDeleteForm($item);

        return $this->render('RBBoltBundle:item:show.html.twig', [
            'item' => $item,
            'delete_form' => $deleteForm->createView(),
        ]);
    }



    /**
     * Displays a form to edit an existing Item entity.
     *
     * @Route("/{id}/edit", name="bolt_item_edit")
     */
    public function editAction(Request $request, Item $item)
    {
        $deleteForm = $this->createDeleteForm($item);
        $editForm   = $this->createForm('RB\BoltBundle\Form\ItemType', $item);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            $r = [
                'infotype' => 'success',
                'msg'      => 'ok'
                ];

            return new JsonResponse($r);
        }

        return $this->render('RBBoltBundle:item:edit.html.twig', [
            'item'     => $item,
            'edit_form'         => $editForm->createView(),
            'delete_form'   => $deleteForm->createView()
        ]);
    }



    /**
     * Batch selected Item entities.
     *
     * @Route("/bolt_item_batch", name="bolt_item_batch")
     */
    public function batchAction(Request $request)
    {

        $items = $request->request->get("items");

        foreach ($items as $key => $item) {
            // $item
        }


        $r = [
            'infotype' => 'success',
            'msg'      => 'batch ok'
        ];

        return new JsonResponse($r);
    }



    /**
     * Finds and displays a Item entity.
     *
     * @Route("/bolt_item_print/{id}", name="bolt_item_print")
     */
    public function printAction(Item $item)
    {

        $r = [
            'infotype' => 'success',
            'msg'      => 'ok',
            'modal'    => [
                'content' => 'print item',
                'title'   => 'item',
                'modal'   => 'modalLg'
            ]
        ];

        return new JsonResponse($r);
    }



    /**
     * Import Item entity.
     *
     * @Route("/bolt_item_import", name="bolt_item_import")
     */
    public function importAction(Item $item)
    {
        $dir_import = $this->container->getParameter('dir_import');
        $list       = $this->get('rb.file')->file($dir_import.'/impex___RBBoltBundle__item.csv');

        $em         = $this->getDoctrine()->getManager();

        $i          = 0;
        $y          = 0;
        $total      = count($list);

        foreach ($list as $key => $d) {
            if($d[0] !='' && $i > 1){

                $item = new item();
                $em->persist($item);
            }
            $i++;
        }
        $em->flush();

        $r = [
            'infotype'  => 'success',
            'msg'       => 'import ok'
        ];

        return new JsonResponse($r);
    }

    /**
     * Export Item entity.
     *
     * @Route("/bolt_item_export", name="bolt_item_export")
     */
    public function exportAction(Item $item)
    {
        $dir_import = $this->container->getParameter('dir_import');
        $file       = $dir_import.'/impex__RBBoltBundle__item.csv';

        $em         = $this->getDoctrine()->getManager();

        $items = $em
          ->getRepository('Item:Item')->findAll();

        $list       = $this->get('rb.file')->toCsv($items, $file);
        $r = [
            'infotype' => 'success',
            'msg'      => 'export ok'
        ];

        return new JsonResponse($r);
    }

    /**
     * Deletes a Item entity.
     *
     * @Route("/{id}", name="bolt_item_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Item $item)
    {
        $form = $this->createDeleteForm($item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();
        }

        return $this->redirectToRoute('bolt_item_index');
    }

    /**
     * Creates a form to delete a Item entity.
     *
     * @param Item $item The Item entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Item $item)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bolt_item_delete', array('id' => $item->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

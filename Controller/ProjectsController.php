<?php

namespace RB\BoltBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use RB\BoltBundle\Entity\Projects;
use RB\BoltBundle\Form\ProjectsType;

/**
 * Projects controller.
 *
 * @Route("/projects")
 */
class ProjectsController extends Controller
{
    /**
     * Lists all Projects entities.
     *
     * @Route("/", name="bolt_projects_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em      = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $ui      = $session->get('ui');
        $view    = (isset($ui['V'])) ?$ui['V']:'index-A';
        $per     = (isset($ui['per'])) ?$ui['per']:25;

        $query   = $em->createQuery('SELECT p FROM RBBoltBundle:Projects p');
        $paginator = $this->get('knp_paginator');
        $projects  = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $per
        );


        return $this->render('RBBoltBundle:project:'.$view.'.html.twig', [
            'projects' => $projects,
        ]);
    }


        /**
    * @Route("/expose",name="bolt_projects_expose")
    */
    public function exposeAction(Request $request)
    {
        
        $em       = $this->getDoctrine()->getManager();
        $session  = $request->getSession();

        $filter   = $request->query->get("filter",'all');

        if ($filter == 'all')
            $entities = $em
                ->getRepository('RBBoltBundle:Projects')
                ->findAll();
        else {
            $entities = $em
                ->getRepository('RBBoltBundle:Projects')
                ->findAll();
            // construct filter sys
        }

        /* SERVICE : rb.serializer */
        $list = $this->get('rb.serializer')->normalize($entities);
        /* END SERVICE :  rb.serializer */

        return new JsonResponse($list);
    }



    /**
     * Creates a new Projects entity.
     *
     * @Route("/new", name="bolt_projects_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        /* SERVICE : router */
        $bolt = $this->get('rb_bolt.admin.services');
        /* END SERVICE :  router */

        $data = [
            'bolt' => $bolt
        ];
        $project = new Projects();
        $form = $this->createForm('RB\BoltBundle\Form\ProjectsType', $project, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            $r = [
                'infotype' => 'success',
                'msg'      => 'ok'
            ];

            return new JsonResponse($r);
        }

        return $this->render('RBBoltBundle:project:new.html.twig', [
            'project' => $project,
            'form'                      => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Projects entity.
     *
     * @Route("/{id}", name="bolt_projects_show")
     * @Method("GET")
     */
    public function showAction(Projects $project)
    {
        $deleteForm = $this->createDeleteForm($project);

        return $this->render('RBBoltBundle:project:show.html.twig', [
            'project' => $project,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Projects entity.
     *
     * @Route("/{id}/edit", name="bolt_projects_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Projects $project)
    {
        /* SERVICE : router */
        $bolt = $this->get('rb_bolt.admin.services');
        /* END SERVICE :  router */

        $data = [
            'bolt'        => $bolt
        ];


        $deleteForm = $this->createDeleteForm($project);
        $editForm   = $this->createForm('RB\BoltBundle\Form\ProjectsType', $project,$data);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            $r = [
                'infotype' => 'success',
                'msg'      => 'ok'
                ];

            return new JsonResponse($r);
        }

        return $this->render('RBBoltBundle:project:edit.html.twig', [
            'project'     => $project,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ]);
    }
  

    /**
     * Deletes a Projects entity.
     *
     * @Route("/{id}", name="bolt_projects_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Projects $project)
    {
        $form = $this->createDeleteForm($project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
        }

        return $this->redirectToRoute('bolt_projects_index');
    }

    /**
     * Creates a form to delete a Projects entity.
     *
     * @param Projects $project The Projects entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Projects $project)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bolt_projects_delete', array('id' => $project->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

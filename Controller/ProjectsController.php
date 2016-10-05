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
        $project = new Projects();
        $form = $this->createForm('RB\BoltBundle\Form\ProjectsType', $project);
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
        $deleteForm = $this->createDeleteForm($project);
        $editForm   = $this->createForm('RB\BoltBundle\Form\ProjectsType', $project);
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
            'edit_form'         => $editForm->createView(),
            'delete_form'   => $deleteForm->createView()
        ]);
    }
    /**
     * Batch selected Projects entities.
     *
     * @Route("/bolt_projects_batch", name="bolt_projects_batch")
     */
    public function batchAction(Request $request)
    {

        $projects = $request->request->get("projects");

        foreach ($projects as $key => $project) {
            // $project
        }




        $r = [
            'infotype' => 'success',
            'msg'      => 'batch ok'
        ];

        return new JsonResponse($r);
    }

    /**
     * Finds and displays a Projects entity.
     *
     * @Route("/bolt_projects_print/{id}", name="bolt_projects_print")
     */
    public function printAction(Projects $project)
    {

        $r = [
            'infotype' => 'success',
            'msg'      => 'ok',
            'modal'    => [
                'content' => 'print project',
                'title'   => 'project',
                'modal'   => 'modalLg'
            ]
        ];

        return new JsonResponse($r);
    }

    /**
     * Import Projects entity.
     *
     * @Route("/bolt_projects_import", name="bolt_projects_import")
     */
    public function importAction(Projects $project)
    {
        $dir_import = $this->container->getParameter('dir_import');
        $list       = $this->get('rb.file')->file($dir_import.'/impex___RBBoltBundle__project.csv');

        $em         = $this->getDoctrine()->getManager();

        $i          = 0;
        $y          = 0;
        $total      = count($list);

        foreach ($list as $key => $d) {
            if($d[0] !='' && $i > 1){

                $project = new project();
                $em->persist($project);
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
     * Export Projects entity.
     *
     * @Route("/bolt_projects_export", name="bolt_projects_export")
     */
    public function exportAction(Projects $project)
    {
        $dir_import = $this->container->getParameter('dir_import');
        $file       = $dir_import.'/impex__RBBoltBundle__project.csv';

        $em         = $this->getDoctrine()->getManager();

        $projects = $em
          ->getRepository('Projects:Projects')->findAll();

        $list       = $this->get('rb.file')->toCsv($projects, $file);
        $r = [
            'infotype' => 'success',
            'msg'      => 'export ok'
        ];

        return new JsonResponse($r);
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

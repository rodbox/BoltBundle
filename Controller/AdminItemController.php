<?php

namespace RB\BoltBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/AdminItem")
 */
class AdminItemController extends Controller
{
    /**
     * @Route("/new", name="/new")
     */
    public function newAction(Request $request)
    {
        return $this->render('RBBoltBundle:AdminItem:new.html.twig', []);
    }

    /**
     * @Route("/edit", name="/edit")
     */
    public function editAction(Request $request)
    {
        return $this->render('RBBoltBundle:AdminItem:edit.html.twig', []);
    }

    /**
     * @Route("/del", name="/del")
     */
    public function delAction(Request $request)
    {
        return $this->render('RBBoltBundle:AdminItem:del.html.twig', []);
    }

    /**
     * @Route("/load", name="/load")
     */
    public function loadAction(Request $request)
    {
        return $this->render('RBBoltBundle:AdminItem:load.html.twig', []);
    }

}

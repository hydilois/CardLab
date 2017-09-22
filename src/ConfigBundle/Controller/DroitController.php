<?php

namespace ConfigBundle\Controller;

use ConfigBundle\Entity\Droit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Droit controller.
 *
 * @Route("droit")
 */
class DroitController extends Controller
{
    /**
     * Lists all droit entities.
     *
     * @Route("/", name="droit_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $droits = $em->getRepository('ConfigBundle:Droit')->findAll();

        return $this->render('droit/index.html.twig', array(
            'droits' => $droits,
        ));
    }

    /**
     * Creates a new droit entity.
     *
     * @Route("/new", name="droit_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $droit = new Droit();
        $form = $this->createForm('ConfigBundle\Form\DroitType', $droit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($droit);
            $em->flush();

            return $this->redirectToRoute('droit_show', array('id' => $droit->getId()));
        }

        return $this->render('droit/new.html.twig', array(
            'droit' => $droit,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a droit entity.
     *
     * @Route("/{id}", name="droit_show")
     * @Method("GET")
     */
    public function showAction(Droit $droit)
    {
        $deleteForm = $this->createDeleteForm($droit);

        return $this->render('droit/show.html.twig', array(
            'droit' => $droit,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing droit entity.
     *
     * @Route("/{id}/edit", name="droit_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Droit $droit)
    {
        $deleteForm = $this->createDeleteForm($droit);
        $editForm = $this->createForm('ConfigBundle\Form\DroitType', $droit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('droit_edit', array('id' => $droit->getId()));
        }

        return $this->render('droit/edit.html.twig', array(
            'droit' => $droit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a droit entity.
     *
     * @Route("/{id}", name="droit_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Droit $droit)
    {
        $form = $this->createDeleteForm($droit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($droit);
            $em->flush();
        }

        return $this->redirectToRoute('droit_index');
    }

    /**
     * Creates a form to delete a droit entity.
     *
     * @param Droit $droit The droit entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Droit $droit)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('droit_delete', array('id' => $droit->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

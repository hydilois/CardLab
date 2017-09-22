<?php

namespace StudentBundle\Controller;

use StudentBundle\Entity\Cycle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Cycle controller.
 *
 * @Route("cycle")
 */
class CycleController extends Controller
{
    /**
     * Lists all cycle entities.
     *
     * @Route("/", name="cycle_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cycles = $em->getRepository('StudentBundle:Cycle')->findAll();

        return $this->render('cycle/index.html.twig', array(
            'cycles' => $cycles,
        ));
    }

    /**
     * Creates a new cycle entity.
     *
     * @Route("/new", name="cycle_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cycle = new Cycle();
        $form = $this->createForm('StudentBundle\Form\CycleType', $cycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cycle);
            $em->flush();

            return $this->redirectToRoute('cycle_show', array('id' => $cycle->getId()));
        }

        return $this->render('cycle/new.html.twig', array(
            'cycle' => $cycle,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a cycle entity.
     *
     * @Route("/{id}", name="cycle_show")
     * @Method("GET")
     */
    public function showAction(Cycle $cycle)
    {
        $deleteForm = $this->createDeleteForm($cycle);

        return $this->render('cycle/show.html.twig', array(
            'cycle' => $cycle,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing cycle entity.
     *
     * @Route("/{id}/edit", name="cycle_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Cycle $cycle)
    {
        $deleteForm = $this->createDeleteForm($cycle);
        $editForm = $this->createForm('StudentBundle\Form\CycleType', $cycle);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cycle_edit', array('id' => $cycle->getId()));
        }

        return $this->render('cycle/edit.html.twig', array(
            'cycle' => $cycle,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a cycle entity.
     *
     * @Route("/{id}", name="cycle_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Cycle $cycle)
    {
        $form = $this->createDeleteForm($cycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cycle);
            $em->flush();
        }

        return $this->redirectToRoute('cycle_index');
    }

    /**
     * Creates a form to delete a cycle entity.
     *
     * @param Cycle $cycle The cycle entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cycle $cycle)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cycle_delete', array('id' => $cycle->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

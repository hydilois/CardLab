<?php

namespace MatiereBundle\Controller;

use MatiereBundle\Entity\EstDispense;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Estdispense controller.
 *
 * @Route("estdispense")
 */
class EstDispenseController extends Controller
{
    /**
     * Lists all estDispense entities.
     *
     * @Route("/", name="estdispense_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $estDispenses = $em->getRepository('MatiereBundle:EstDispense')->findAll();

        return $this->render('estdispense/index.html.twig', array(
            'estDispenses' => $estDispenses,
        ));
    }

    /**
     * Creates a new estDispense entity.
     *
     * @Route("/new", name="estdispense_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $estDispense = new Estdispense();
        $form = $this->createForm('MatiereBundle\Form\EstDispenseType', $estDispense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($estDispense);
            $em->flush();

            return $this->redirectToRoute('estdispense_show', array('id' => $estDispense->getId()));
        }

        return $this->render('estdispense/new.html.twig', array(
            'estDispense' => $estDispense,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a estDispense entity.
     *
     * @Route("/{id}", name="estdispense_show")
     * @Method("GET")
     */
    public function showAction(EstDispense $estDispense)
    {
        $deleteForm = $this->createDeleteForm($estDispense);

        return $this->render('estdispense/show.html.twig', array(
            'estDispense' => $estDispense,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing estDispense entity.
     *
     * @Route("/{id}/edit", name="estdispense_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, EstDispense $estDispense)
    {
        $deleteForm = $this->createDeleteForm($estDispense);
        $editForm = $this->createForm('MatiereBundle\Form\EstDispenseType', $estDispense);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('estdispense_edit', array('id' => $estDispense->getId()));
        }

        return $this->render('estdispense/edit.html.twig', array(
            'estDispense' => $estDispense,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a estDispense entity.
     *
     * @Route("/{id}", name="estdispense_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EstDispense $estDispense)
    {
        $form = $this->createDeleteForm($estDispense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($estDispense);
            $em->flush();
        }

        return $this->redirectToRoute('estdispense_index');
    }

    /**
     * Creates a form to delete a estDispense entity.
     *
     * @param EstDispense $estDispense The estDispense entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EstDispense $estDispense)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('estdispense_delete', array('id' => $estDispense->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

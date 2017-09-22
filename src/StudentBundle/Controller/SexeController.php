<?php

namespace StudentBundle\Controller;

use StudentBundle\Entity\Sexe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Sexe controller.
 *
 * @Route("sexe")
 */
class SexeController extends Controller
{
    /**
     * Lists all sexe entities.
     *
     * @Route("/", name="sexe_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sexes = $em->getRepository('StudentBundle:Sexe')->findAll();

        return $this->render('sexe/index.html.twig', array(
            'sexes' => $sexes,
        ));
    }

    /**
     * Creates a new sexe entity.
     *
     * @Route("/new", name="sexe_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $sexe = new Sexe();
        $form = $this->createForm('StudentBundle\Form\SexeType', $sexe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sexe);
            $em->flush();

            return $this->redirectToRoute('sexe_show', array('id' => $sexe->getId()));
        }

        return $this->render('sexe/new.html.twig', array(
            'sexe' => $sexe,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a sexe entity.
     *
     * @Route("/{id}", name="sexe_show")
     * @Method("GET")
     */
    public function showAction(Sexe $sexe)
    {
        $deleteForm = $this->createDeleteForm($sexe);

        return $this->render('sexe/show.html.twig', array(
            'sexe' => $sexe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sexe entity.
     *
     * @Route("/{id}/edit", name="sexe_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Sexe $sexe)
    {
        $deleteForm = $this->createDeleteForm($sexe);
        $editForm = $this->createForm('StudentBundle\Form\SexeType', $sexe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sexe_edit', array('id' => $sexe->getId()));
        }

        return $this->render('sexe/edit.html.twig', array(
            'sexe' => $sexe,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a sexe entity.
     *
     * @Route("/{id}", name="sexe_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Sexe $sexe)
    {
        $form = $this->createDeleteForm($sexe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($sexe);
            $em->flush();
        }

        return $this->redirectToRoute('sexe_index');
    }

    /**
     * Creates a form to delete a sexe entity.
     *
     * @param Sexe $sexe The sexe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Sexe $sexe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sexe_delete', array('id' => $sexe->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

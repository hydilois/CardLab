<?php

namespace ConfigBundle\Controller;

use ConfigBundle\Entity\Annee;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Annee controller.
 *
 * @Route("annee")
 */
class AnneeController extends Controller
{
    /**
     * Lists all annee entities.
     *
     * @Route("/", name="annee_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $annees = $em->getRepository('ConfigBundle:Annee')->findAll();

        return $this->render('annee/index.html.twig', array(
            'annees' => $annees,
        ));
    }

    /**
     * Creates a new annee entity.
     *
     * @Route("/new", name="annee_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $annee = new Annee();
        $form = $this->createForm('ConfigBundle\Form\AnneeType', $annee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $annee->setAnneeScolaire($annee->getAnneeDebut().'/'.$annee->getAnneeFin());
            $em->persist($annee);
            $em->flush();

            return $this->redirectToRoute('annee_index');
        }

        return $this->render('annee/new.html.twig', array(
            'annee' => $annee,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a annee entity.
     *
     * @Route("/{id}", name="annee_show")
     * @Method("GET")
     */
    public function showAction(Annee $annee)
    {
        $deleteForm = $this->createDeleteForm($annee);

        return $this->render('annee/show.html.twig', array(
            'annee' => $annee,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing annee entity.
     *
     * @Route("/{id}/edit", name="annee_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Annee $annee)
    {
        $deleteForm = $this->createDeleteForm($annee);
        $editForm = $this->createForm('ConfigBundle\Form\AnneeType', $annee);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $annee->setAnneeScolaire($annee->getAnneeDebut().'/'.$annee->getAnneeFin());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('annee_index');
        }

        return $this->render('annee/edit.html.twig', array(
            'annee' => $annee,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a annee entity.
     *
     * @Route("/{id}", name="annee_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Annee $annee)
    {
        $form = $this->createDeleteForm($annee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($annee);
            $em->flush();
        }

        return $this->redirectToRoute('annee_index');
    }

    /**
     * Creates a form to delete a annee entity.
     *
     * @param Annee $annee The annee entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Annee $annee)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('annee_delete', array('id' => $annee->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

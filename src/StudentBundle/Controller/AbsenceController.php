<?php

namespace StudentBundle\Controller;

use StudentBundle\Entity\Absence;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Absence controller.
 *
 * @Route("absence")
 */
class AbsenceController extends Controller
{
    /**
     * Lists all absence entities.
     *
     * @Route("/", name="absence_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $absences = $em->getRepository('StudentBundle:Absence')->findAll();

        return $this->render('absence/index.html.twig', array(
            'absences' => $absences,
        ));
    }

    /**
     * Creates a new absence entity.
     *
     * @Route("/new", name="absence_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $absence = new Absence();
        $form = $this->createForm('StudentBundle\Form\AbsenceType', $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($absence);
            $em->flush();

            return $this->redirectToRoute('absence_show', array('id' => $absence->getId()));
        }

        return $this->render('absence/new.html.twig', array(
            'absence' => $absence,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a absence entity.
     *
     * @Route("/{id}", name="absence_show")
     * @Method("GET")
     */
    public function showAction(Absence $absence)
    {
        $deleteForm = $this->createDeleteForm($absence);

        return $this->render('absence/show.html.twig', array(
            'absence' => $absence,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing absence entity.
     *
     * @Route("/{id}/edit", name="absence_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Absence $absence)
    {
        $deleteForm = $this->createDeleteForm($absence);
        $editForm = $this->createForm('StudentBundle\Form\AbsenceType', $absence);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('absence_edit', array('id' => $absence->getId()));
        }

        return $this->render('absence/edit.html.twig', array(
            'absence' => $absence,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a absence entity.
     *
     * @Route("/{id}", name="absence_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Absence $absence)
    {
        $form = $this->createDeleteForm($absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($absence);
            $em->flush();
        }

        return $this->redirectToRoute('absence_index');
    }

    /**
     * Creates a form to delete a absence entity.
     *
     * @param Absence $absence The absence entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Absence $absence)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('absence_delete', array('id' => $absence->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

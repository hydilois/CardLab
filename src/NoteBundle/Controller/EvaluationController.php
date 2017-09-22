<?php

namespace NoteBundle\Controller;

use NoteBundle\Entity\Evaluation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Evaluation controller.
 *
 * @Route("evaluation")
 */
class EvaluationController extends Controller
{
    /**
     * Lists all evaluation entities.
     *
     * @Route("/", name="evaluation_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $evaluations = $em->getRepository('NoteBundle:Evaluation')->findAll();

        return $this->render('evaluation/index.html.twig', array(
            'evaluations' => $evaluations,
        ));
    }

    /**
     * Creates a new evaluation entity.
     *
     * @Route("/new", name="evaluation_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $evaluation = new Evaluation();
        $form = $this->createForm('NoteBundle\Form\EvaluationType', $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($evaluation);
            $em->flush();

            return $this->redirectToRoute('evaluation_show', array('id' => $evaluation->getId()));
        }

        return $this->render('evaluation/new.html.twig', array(
            'evaluation' => $evaluation,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a evaluation entity.
     *
     * @Route("/{id}", name="evaluation_show")
     * @Method("GET")
     */
    public function showAction(Evaluation $evaluation)
    {
        $deleteForm = $this->createDeleteForm($evaluation);

        return $this->render('evaluation/show.html.twig', array(
            'evaluation' => $evaluation,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing evaluation entity.
     *
     * @Route("/{id}/edit", name="evaluation_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Evaluation $evaluation)
    {
        $deleteForm = $this->createDeleteForm($evaluation);
        $editForm = $this->createForm('NoteBundle\Form\EvaluationType', $evaluation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evaluation_edit', array('id' => $evaluation->getId()));
        }

        return $this->render('evaluation/edit.html.twig', array(
            'evaluation' => $evaluation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a evaluation entity.
     *
     * @Route("/{id}", name="evaluation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Evaluation $evaluation)
    {
        $form = $this->createDeleteForm($evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($evaluation);
            $em->flush();
        }

        return $this->redirectToRoute('evaluation_index');
    }

    /**
     * Creates a form to delete a evaluation entity.
     *
     * @param Evaluation $evaluation The evaluation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Evaluation $evaluation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('evaluation_delete', array('id' => $evaluation->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

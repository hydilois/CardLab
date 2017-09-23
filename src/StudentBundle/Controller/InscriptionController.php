<?php

namespace StudentBundle\Controller;

use StudentBundle\Entity\Inscription;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use ConfigBundle\Entity\Annee;
use StudentBundle\Entity\Student;

/**
 * Inscription controller.
 *
 * @Route("inscription")
 */
class InscriptionController extends Controller {

    /**
     * Lists all inscription entities.
     *
     * @Route("/", name="inscription_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $inscriptions = $em->getRepository('StudentBundle:Inscription')->findAll();

        return $this->render('inscription/index.html.twig', array(
                    'inscriptions' => $inscriptions,
        ));
    }

    /**
     * Creates a new inscription entity.
     *
     * @Route("/new-{studentId}", name="inscription_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $studentId = 0) {
        $em = $this->getDoctrine()->getManager();
        $inscription = new Inscription();

        $student = $em->getRepository('StudentBundle:Student')->find($studentId);
        if ($student != NULL) {
            $inscription->setStudent($student);
        }

        $annee = $em->getRepository('ConfigBundle:Annee')->findOneBy(['isAnneeEnCour' => true]);
        $inscription->setAnnee($annee);
        $form = $this->createForm('StudentBundle\Form\InscriptionType', $inscription);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $inscription->setAnnee($annee);
            $inscription->setStudent($student);

            $em = $this->getDoctrine()->getManager();
            $em->persist($inscription);
            $em->flush();

            return $this->redirectToRoute('inscription_show', array('id' => $inscription->getId()));
        }

        return $this->render('inscription/new.html.twig', array(
                    'inscription' => $inscription,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing inscription entity.
     *
     * @Route("/{id}/edit", name="inscription_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Inscription $inscription) {
        $deleteForm = $this->createDeleteForm($inscription);
        $editForm = $this->createForm('StudentBundle\Form\InscriptionType', $inscription);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('inscription_edit', array('id' => $inscription->getId()));
        }

        return $this->render('inscription/edit.html.twig', array(
                    'inscription' => $inscription,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a inscription entity.
     *
     * @Route("/{id}", name="inscription_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Inscription $inscription) {
        $form = $this->createDeleteForm($inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($inscription);
            $em->flush();
        }

        return $this->redirectToRoute('inscription_index');
    }

    /**
     * Liste des eleves non inscrits
     * 
     * @Route("/aInscrire", name="inscription_notyet")
     * @Method("GET")
     */
    public function aInscrireAction() {
        $em = $this->getDoctrine()->getManager();
        $annee = $em->getRepository('ConfigBundle:Annee')->findOneBy(['isAnneeEnCour' => true]);
        // $anneEncour = '';
        // if ($annee != null) {
        //     $anneEncour = $annee->getAnneeScolaire();
        // }

        $subQueryBuilder = $em->createQueryBuilder();
        $subQuery = $subQueryBuilder
                ->select('IDENTITY(i.student)')
                ->from('StudentBundle:Inscription', 'i')
                ->where('i.annee= :annee')
                ->setParameters(array(
                    'annee' => $annee,
                ))
                ->getQuery()
                ->getArrayResult();

                
        if ($subQuery) {
            $queryBuilder = $em->createQueryBuilder();
            $query = $queryBuilder
                    ->select('s')
                    ->from('StudentBundle:Student', 's')
                    ->where($queryBuilder->expr()->notIn('s.id', ':subQuery'))
                    ->setParameter('subQuery', $subQuery)
                    ->getQuery();

            $students = $query->getResult();
        } else {
            $students = $this->getDoctrine()->getRepository('StudentBundle:Student')->findAll();
        }

        $incriptionsNonComplets = $this->getDoctrine()->getRepository('StudentBundle:Inscription')->findBy(
                array(
                    'status' => 0,
                    'annee' => $annee,
        ));

        return $this->render('inscription/listeElevesNonInscrits.html.twig', array(
                    'entities' => $students,
                    'incriptionsNonCompletes' => $incriptionsNonComplets,
        ));
    }

    /**
     * Creates a form to delete a inscription entity.
     *
     * @param Inscription $inscription The inscription entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Inscription $inscription) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('inscription_delete', array('id' => $inscription->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * Finds and displays a inscription entity.
     *
     * @Route("/{id}", name="inscription_show")
     * @Method("GET")
     */
    public function showAction(Inscription $inscription) {
        $deleteForm = $this->createDeleteForm($inscription);

        return $this->render('inscription/show.html.twig', array(
                    'inscription' => $inscription,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

}

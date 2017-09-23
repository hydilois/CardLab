<?php

namespace StudentBundle\Controller;

use StudentBundle\Entity\Absence;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Absence controller.
 *
 * @Route("absence")
 */
class AbsenceController extends Controller {

    /**
     * Creates a new absence entity.
     *
     * @Route("/Classe-{idClasse}/Sequence-{idSeq}", name="absence_classe")
     * @Method({"GET", "POST"})
     */
    public function absenceClasseAction(Request $request, $idClasse, $idSeq) {
        $em = $this->getDoctrine();
        $anneeEnCours = $this->getDoctrine()->getRepository('ConfigBundle:Annee')->findOneBy(['isAnneeEnCour' => true]);

        $classe = $em->getRepository('StudentBundle:Classe')
                ->find($idClasse);
        $sequence = $em->getRepository('NoteBundle:Sequence')
                ->find($idSeq);

        $listEleve = $em->getRepository('StudentBundle:Inscription')
                ->findBy(
                array(
                    'classe' => $classe,
                    'annee' => $anneeEnCours
                )
        );
        foreach ($listEleve as $eleve) {
            $abs = $em->getRepository('StudentBundle:Absence')
                    ->findOneBy(
                    array(
                        'student' => $eleve,
                        'anneeScolaire' => $anneeEnCours,
                        'sequence' => $sequence
                    )
            );
            if ($abs != NULL) {
                $eleve->setNbreAbsence($abs->getNbreAbsence());
            }
        }

        if ($request->getMethod() == 'POST') {
            foreach ($listEleve as $eleve) {
                $absence = $em->getRepository('StudentBundle:Absence')
                        ->findOneBy(
                        array(
                            'student' => $eleve,
                            'anneeScolaire' => $anneeEnCours,
                            'sequence' => $sequence
                        )
                );
                if ($absence != NULL) {
                    $absence->setUpNbreAbsence($_POST['el_' . $eleve->getId()]);
                    $em->getManager()->flush();
                } else {
                    $absence = new Absence();
                    $absence->setStudent($eleve);
                    $absence->setAnneeScolaire($anneeEnCours);
                    $absence->setSequence($sequence);
                    $absence->setUpNbreAbsence($_POST['el_' . $eleve->getId()]);
                    $em->getManager()->persist($absence);
                    $em->getManager()->flush();
                }
            }
            return $this->redirect($this->generateUrl('absence_index'));
        }

        return $this->render('absence/absenceClasse.html.twig', array(
                    'listEleves' => $listEleve,
                    'sequence' => $sequence,
                    'annee' => $anneeEnCours,
                    'classe' => $classe
        ));
    }

    /**
     * Lists all absence entities.
     *
     * @Route("/", name="absence_index")
     * @Method("GET")
     */
    public function indexAction() {
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
    public function newAction(Request $request) {
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
    public function showAction(Absence $absence) {
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
    public function editAction(Request $request, Absence $absence) {
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
    public function deleteAction(Request $request, Absence $absence) {
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
    private function createDeleteForm(Absence $absence) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('absence_delete', array('id' => $absence->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}

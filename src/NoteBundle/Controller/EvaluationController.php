<?php

namespace NoteBundle\Controller;

use NoteBundle\Entity\Evaluation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use ConfigBundle\Entity\Ecole;
use MatiereBundle\Entity\EstDispense;
use NoteBundle\Form\EvaluationType;
use StudentBundle\Entity\Classe;

/**
 * Evaluation controller.
 *
 * @Route("evaluation")
 */
class EvaluationController extends Controller {

    /**
     * Lists all evaluation entities.
     *
     * @Route("/", name="evaluation_index")
     * @Method("GET")
     */
    public function indexAction() {
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
    public function newAction(Request $request) {
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
    public function showAction(Evaluation $evaluation) {
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
    public function editAction(Request $request, Evaluation $evaluation) {
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
    public function deleteAction(Request $request, Evaluation $evaluation) {
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
     * Enregistrement des notes
     * @Route("/classe-{id}/sequence-{idSeq}/matiere-{idMat}/notes/add", name="enregistrement_note")
     * @Method({"GET", "POST"})
     */
    public function noteAction(Request $request, $id, $idSeq, $idMat) {

        $em = $this->getDoctrine()->getManager();

        if (((int) $idMat) == 0 || (int) $idSeq == 0) {

            goto suite;
        }

        $classe = $em->getRepository('StudentBundle:Classe')->find($id);
        $sequence = $em->getRepository('NoteBundle:Sequence')->find($idSeq);
        $matiere = $em->getRepository('MatiereBundle:Matiere')->find($idMat);
        $anneeEnCour = $em->getRepository('ConfigBundle:Annee')->findOneBy(['isAnneeEnCour' => true]);

        $subQueryBuilder = $em->createQueryBuilder();
        $subQuery = $subQueryBuilder
                ->select('IDENTITY(e.student)')
                ->from('NoteBundle:Evaluation', 'e')
                ->where('e.annee= :annee')
                ->andWhere('e.sequence= :sequence')
                ->andWhere('e.matiere= :matiere')
                ->setParameters(array(
                    'annee' => $anneeEnCour,
                    'sequence' => $sequence,
                    'matiere' => $matiere,
                ))
                ->getQuery()
                ->getArrayResult();
        if (!$subQuery) {
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();

            $qb->select('s')
                    ->from('StudentBundle:Student', 's')
                    ->innerJoin('StudentBundle:Inscription', 'i', 'WITH', 'i.student = s.id')
                    ->innerJoin('StudentBundle:Classe', 'c', 'WITH', 'i.classe = c.id')
                    ->where('c.id = :identifie')
                    ->setParameter('identifie', $id);
            $eleves = $qb->getQuery()->getResult();
        } else {

            $queryBuilder = $em->createQueryBuilder();
            $query = $queryBuilder
                    ->select('s')
                    ->from('StudentBundle:Student', 's')
                    ->innerJoin('StudentBundle:Inscription', 'i', 'WITH', 'i.student = s.id')
                    ->innerJoin('StudentBundle:Classe', 'c', 'WITH', 'i.classe = c.id')
                    ->where('c.id = :idClasse')
                    ->andWhere($queryBuilder->expr()->notIn('s.id', ':subQuery'))
                    ->setParameters(array(
                        'subQuery' => $subQuery,
                        'idClasse' => $id,
                    ))
                    ->getQuery();

            $eleves = $query->getResult();
        }

        if ($request->getMethod() == 'POST') {

            foreach ($eleves as $elev) {
                $qbEvaluation = $this->getDoctrine()->getManager()->createQueryBuilder();
                $qbEvaluation->select('e')
                        ->from('NoteBundle:Evaluation', 'e')
                        ->where('e.sequence = :idSequence')
                        ->andWhere('e.student = :idEleve')
                        ->andWhere('e.matiere = :idMatiere')
                        ->setParameters(array(
                            'idEleve' => $elev->getId(),
                            'idSequence' => $idSeq,
                            'idMatiere' => $idMat
                ));
                $evaluationExist = $qbEvaluation->getQuery()->getResult();
                if ($evaluationExist) {
                    
                } else {
                    $evaluation = new Evaluation();
                    $note = $request->request->get($elev->getId());
                    //die('Bonjour' . $note);
                    if ($note < 0 || $note > 20) {
                        $request->getSession()->getFlashBag()->add('error', 'Note de ' . $elev->getNom() . ' incorecte');
                    } else if (is_float($note + 0.0) && is_numeric($note)) {
                        $evaluation->setNote($note);
                        $evaluation->setSequence($sequence);
                        $evaluation->setStudent($elev);
                        $evaluation->setMatiere($matiere);
                        $evaluation->setAnnee($anneeEnCour);
                        $evaluation->setClasse($classe);
                        $em->persist($evaluation);
                        $em->flush();
                    } else if (!(ctype_digit($note))) {
                        $request->getSession()->getFlashBag()->add('error', 'Note de ' . $elev->getNom() . ' incorecte');
                    } else {
                        //Dans le cas où tous les tests ne sont pas satisfaisants
                        $evaluation->setNote($note);
                        $evaluation->setSequence($sequence);
                        $evaluation->setStudent($elev);
                        $evaluation->setMatiere($matiere);
                        $evaluation->setAnnee($anneeEnCour);
                        $evaluation->setClasse($classe);
                        $em->persist($evaluation);
                        $em->flush();
                    }
                }
            }
            suite:
            return $this->redirect($this->generateUrl('homepage'));
        }
        $enseignement = $em->getRepository('MatiereBundle:EstDispense')->findOneBy(['classe' => $classe, 'annee' => $anneeEnCour, 'matiere' => $matiere]);
        return $this->render('evaluation/notes.html.twig', array(
                    'classe' => $classe,
                    'sequence' => $sequence,
                    'matiere' => $matiere,
                    'annee' => $anneeEnCour,
                    'eleves' => $eleves,
                    'enseignement' => $enseignement
        ));
    }

    /**
     * Activités des classes
     *
     * @Route("/classes/activites", name="classe_activite")
     * @Method("GET")
     */
    public function afficheNoteAction() {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('cl')->from('StudentBundle:Classe', 'cl')->where('cl.classePere IS NOT NULL');
        $classes = $qb->getQuery()->getResult();

        $sequences = $em->getRepository('NoteBundle:Sequence')->findAll();
        $matieres = $em->getRepository('MatiereBundle:Matiere')->findAll();
        $annee = $em->getRepository('ConfigBundle:Annee')->findOneBy(['isAnneeEnCour' => true]);

        return $this->render('evaluation/choixClasse.html.twig', array(
                    'classes' => $classes,
                    'sequences' => $sequences,
                    'matieres' => $matieres
        ));
    }

    /**
     * Activités des classes
     *
     * @Route("/classes-{idClasse}/enseignement", name="classe_enseignement")
     * @Method("GET")
     */
    public function enseignementAction($idClasse) {
        $enseignement = new EstDispense();
        $em = $this->getDoctrine()->getManager();
        $classe = $em->getRepository('StudentBundle:Classe')->find($idClasse);
        $annee = $em->getRepository('ConfigBundle:Annee')->findOneBy(['isAnneeEnCour' => true]);

        $enseignement = $em->getRepository('MatiereBundle:EstDispense')->findBy(['classe' => $classe, 'annee' => $annee]);

        return $this->render('evaluation/enseignement.html.twig', array(
                    'enseignements' => $enseignement,
                    'classe' => $classe
        ));
    }

    /**
     * Creates a form to delete a evaluation entity.
     *
     * @param Evaluation $evaluation The evaluation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Evaluation $evaluation) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('evaluation_delete', array('id' => $evaluation->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}

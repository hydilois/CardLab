<?php

namespace NoteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ConfigBundle\Entity\Ecole;
use MatiereBundle\Entity\EstDispense;
use NoteBundle\Form\EvaluationType;
use StudentBundle\Entity\Classe;
use NoteBundle\Entity\Evaluation;
use UserBundle\Entity\Utilisateur;

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
     * Formulaire de Pre-statistique
     *
     * @Route("/Statistiques-par-Sequence", name="statSeq")
     * @Method("GET")
     */
    public function statSeqMatAction() {
        $em = $this->getDoctrine()->getManager();
        $annee = $em->getRepository('ConfigBundle:Annee')->findOneBy(['isAnneeEnCour' => true]);

        $listeEnseignements = $em->getRepository('MatiereBundle:EstDispense')->findBy(array(
            'annee' => $annee,
        ));
        $sequence = $em->getRepository('NoteBundle:Sequence')->findAll();

        return $this->render('evaluation/statSequentiel.html.twig', array(
                    'enseignements' => $listeEnseignements,
                    'sequences' => $sequence,
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
            return $this->redirect($this->generateUrl('classe_activite'));
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

        $classes = $em->getRepository('StudentBundle:Classe')->findBy(['classePere' => !NULL]);
        $qb = $em->createQueryBuilder();
        $qb->select('cl')
                ->from('StudentBundle:Classe', 'cl')
                ->where('cl.classePere IS NOT NULL');

        $classes = $qb->getQuery()->getResult();
        $sequences = $em->getRepository('NoteBundle:Sequence')->findAll();
        $matieres = $em->getRepository('MatiereBundle:Matiere')->findAll();
        $annee = $em->getRepository('ConfigBundle:Annee')->findOneBy(['isAnneeEnCour' => true]);

        return $this->render('evaluation/choixClasse.html.twig', array(
                    'classes' => $classes,
                    'sequences' => $sequences,
                    'matieres' => $matieres,
                    'annee' => $annee,
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
                    'classe' => $classe,
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

    /**
     * Formulaire de Pre-statistique
     *
     * @Route("/{idSequence}/{idMatiere}/{idEnseignant}/statistiques/info", name="statistiques_info")
     * @Method("GET")
     */
    public function infoStatistiquesAction($idSequence, $idMatiere, $idEnseignant) {
        $em = $this->getDoctrine()->getManager();

        $annee = $em->getRepository('ConfigBundle:Annee')->findOneBy(['isAnneeEnCour' => true]);

        $sequence = $em->getRepository('NoteBundle:Sequence')->find($idSequence);
        $matiere = $em->getRepository('MatiereBundle:Matiere')->find($idMatiere);
        $enseignant = $em->getRepository('UserBundle:Utilisateur')->find($idEnseignant);


        $listeEnseignements = $em->getRepository('MatiereBundle:EstDispense')->findBy(array(
            'annee' => $annee,
            'enseignant' => $enseignant,
            'matiere' => $matiere,
        ));

        return $this->render('evaluation/formulaireStatistique.html.twig', array(
                    'sequence' => $sequence,
                    'matiere' => $matiere,
                    'enseignant' => $enseignant,
                    'enseignements' => $listeEnseignements,
        ));
    }

    /**
     * Formulaire de Pre-statistique
     *
     * @Route("/{idSequence}/{idMatiere}/{idEnseignant}/statistiques", name="statistiques")
     * @Method("POST")
     */
    public function statistiquesSequenceAction(Request $request, $idSequence, $idMatiere, $idEnseignant) {

        $em = $this->getDoctrine()->getManager();

        //$school = $this->getDoctrine()->getRepository('ConfigBundle:Ecole')->findAll();
        $constante = $em->getRepository('ConfigBundle:Pays')->findAll();
        //$ecole = $school[0];

        $school = $em->getRepository('ConfigBundle:Constante')->findAll();

        $sequence = $em->getRepository('NoteBundle:Sequence')->find($idSequence);
        $matiere = $em->getRepository('MatiereBundle:Matiere')->find($idMatiere);
        $enseignant = $em->getRepository('UserBundle:Utilisateur')->find($idEnseignant);
        $annee = $em->getRepository('ConfigBundle:Annee')->findOneBy(['isAnneeEnCour' => true]);


        $listeEnseignements = $em->getRepository('MatiereBundle:EstDispense')->findBy(array(
            'annee' => $annee,
            'enseignant' => $enseignant,
            'matiere' => $matiere,
        ));
        if ($request->getMethod() == "POST") {
            $date = [];
            $date[] = $request->get('dateDebut');
            $date[] = $request->get('dateFin');
            $compteurTotalMoyenne = 0;
            $compteurTotalEvaluations = 0;
            foreach ($listeEnseignements as $enseignement) {
                $qb2 = $em->createQueryBuilder();
                $qb2->select('s')
                        ->from('StudentBundle:Student', 's')
                        ->innerJoin('StudentBundle:Inscription', 'i', 'WITH', 'i.student = s.id')
                        ->innerJoin('StudentBundle:Classe', 'c', 'WITH', 'i.classe = c.id')
                        ->innerJoin('MatiereBundle:EstDispense', 'e', 'WITH', 'e.classe = c.id')
                        ->innerJoin('StudentBundle:Sexe', 'se', 'WITH', 'se.id = s.sexe')
                        ->where('i.annee= :anneeEnCour')
                        ->andWhere('c.id = :idClasse')
                        ->andWhere('se.nom = :sexe')
                        ->setParameters(array(
                            'anneeEnCour' => $enseignement->getAnnee(),
                            'idClasse' => $enseignement->getClasse()->getId(),
                            'sexe' => 'FEMININ',
                ));
                $filles = $qb2->getQuery()->getResult();

                $qb1 = $em->createQueryBuilder();
                $qb1->select('s')
                        ->from('StudentBundle:Student', 's')
                        ->innerJoin('StudentBundle:Inscription', 'i', 'WITH', 'i.student = s.id')
                        ->innerJoin('StudentBundle:Classe', 'c', 'WITH', 'i.classe = c.id')
                        ->innerJoin('MatiereBundle:EstDispense', 'e', 'WITH', 'e.classe = c.id')
                        ->innerJoin('StudentBundle:Sexe', 'se', 'WITH', 'se.id = s.sexe')
                        ->where('i.annee= :anneeEnCour')
                        ->andWhere('c.id = :idClasse')
                        ->andWhere('se.nom = :sexe')
                        ->setParameters(array(
                            'anneeEnCour' => $enseignement->getAnnee(),
                            'idClasse' => $enseignement->getClasse()->getId(),
                            'sexe' => 'MASCULIN',
                ));
                $garcons = $qb1->getQuery()->getResult();

                $enseignement->setNbreFilles(count($filles));
                $enseignement->setNbreGarcons(count($garcons));

                $enseignement->setNbreHeures($request->get($enseignement->getId() . "-heures"));
                $enseignement->setNbreLecons($request->get($enseignement->getId() . "-lecons"));
                $evaluations = [];
                $evaluations = $em->getRepository('NoteBundle:Evaluation')->findBy(array(
                    'sequence' => $sequence,
                    'matiere' => $matiere,
                    'annee' => $annee,
                    'classe' => $enseignement->getClasse(),
                ));
                $compt = 0;
                $comptGarcons = 0;
                $comptFilles = 0;
                $moyenneGenerale = 0;

                $compteur0_999 = 0;
                $compteur10_1199 = 0;
                $compteur12_1399 = 0;
                $compteur14_1599 = 0;
                $compteur16_20 = 0;
                $listeNotes = [];
                foreach ($evaluations as $moy) {
                    if ($moy->getNote() >= 10) {
                        $compt += 1;
                        if ($moy->getStudent()->getSexe() == "MASCULIN") {
                            $comptGarcons += 1;
                        } else {
                            $comptFilles += 1;
                        }
                    }
                    $moyenneGenerale = $moyenneGenerale + $moy->getNote();
                    //Gestion des intervalles de notes
                    if ($moy->getNote() >= 16) {
                        $compteur16_20 += 1;
                    } else if ($moy->getNote() < 16 && $moy->getNote() >= 14) {
                        $compteur14_1599 += 1;
                    } else if ($moy->getNote() < 14 && $moy->getNote() >= 12) {
                        $compteur12_1399 += 1;
                    } else if ($moy->getNote() < 12 && $moy->getNote() >= 10) {
                        $compteur10_1199 += 1;
                    } else if ($moy->getNote() < 10 && $moy->getNote() >= 0) {
                        $compteur0_999 += 1;
                    }
                }
                $compteurTotalMoyenne += $compt;
                $compteurTotalEvaluations += count($evaluations);

                $enseignement->setCompteurFilles($comptFilles);
                $enseignement->setCompteurGarcons($comptGarcons);
                $enseignement->setNbreEvaluations(count($evaluations));
                if (count($evaluations) != 0) {
                    $enseignement->setMoyenneGenerale($moyenneGenerale / count($evaluations));
                }
                $listeNotes[] = $compteur16_20;
                $listeNotes[] = $compteur14_1599;
                $listeNotes[] = $compteur12_1399;
                $listeNotes[] = $compteur10_1199;
                $listeNotes[] = $compteur0_999;

                $enseignement->setListeNotes($listeNotes);
                $listeNotes = [];
            }
        }

        $html = $this->renderView('evaluation/statistiques.html.twig', array(
            'sequence' => $sequence,
            'date' => $date,
            'matiere' => $matiere,
            'ecole' => $school[0],
            'pays' => $constante[0],
            'enseignant' => $enseignant,
            'enseignements' => $listeEnseignements,
            'nbreMoyennes' => $compteurTotalMoyenne,
            'nbreEvaluations' => $compteurTotalEvaluations,
        ));

        $html2pdf = $this->get('html2pdf_factory')->create('L', 'A4', 'fr', true, 'UTF-8', array(10, 5, 10, 5));
        $html2pdf->pdf->SetAuthor('GreenSoft-Team');
        $html2pdf->pdf->SetTitle('Statistiques ' . $enseignant->getNom() . ' ' . $sequence->getNom());
        $html2pdf->pdf->SetSubject('Statitiques Sequentiel');
        $html2pdf->pdf->SetKeywords('Classe, Enseignant, Matiere, Sequence');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);

        $content = $html2pdf->Output('', true);
        $response = new Response();
        $response->setContent($content);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-disposition', 'filename=StatistiquesSequentiels.pdf');
        return $response;
    }

}

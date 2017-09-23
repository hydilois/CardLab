<?php

namespace NoteBundle\Controller;

use MatiereBundle\Entity\Categorie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use NoteBundle\Entity\Evaluation;
use NoteBundle\Entity\Sequence;
use ConfigBundle\Entity\Ecole;
use ConfigBundle\Entity\Constante;
use StudentBundle\Entity\Inscription;
use MatiereBundle\Entity\EstDispense;
use MatiereBundle\Entity\Matiere;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Description of BulletinController
 * @author atbr
 * @Route("bulletin")
 */
class BulletinController extends Controller {

    /**
     * Lists all evaluation entities.
     *
     * @Route("/", name="evaluation_index")
     * @Method("GET")
     */
    public function indexAction() {
        return $this->render('SchoolNoteBundle:Bulletin:bulletinSeq.html.twig');
    }

    /**
     * Lists all evaluation entities.
     *
     * @Route("/classe-{idClasse}/eleve-{idEleve}/sequence-{idSeq}/annee-{idAnnee}", name="bulletin_sequentiel_eleve")
     * @Method("GET")
     */
    public function bulletinStudentAction($idEleve, $idSeq, $idAnnee, $idClasse) {
        $school = $this->getDoctrine()->getRepository('SchoolConfigBundle:Ecole')->findAll();
        $constante = $this->getDoctrine()->getRepository('SchoolConfigBundle:Constante')->findAll();
        $ecole = $school[0];
        $pays = $constante[0];

        $eleve = $this->getDoctrine()->getRepository('SchoolStudentBundle:Student')->find($idEleve);
        $classe = $this->getDoctrine()->getRepository('SchoolStudentBundle:Classe')->find($idClasse);
        $sequence = $this->getDoctrine()->getRepository('SchoolNoteBundle:Sequence')->find($idSeq);
        $anneeScolaire = $this->getDoctrine()->getRepository('SchoolConfigBundle:Annee')->find($idAnnee);

        $Allstudent = $this->getDoctrine()->getRepository('SchoolStudentBundle:Inscription')->findBy(
            array(
                'classe' => $classe,
                'annee' => $anneeScolaire
            ));

        if (($eleve != NULL) && ($sequence != NULL) && ($anneeScolaire != NULL) && ($classe != NULL)) {

            $listCategorie = $this->getDoctrine()->getRepository('SchoolMatiereBundle:Categorie')->findAll();
            $dispense = $this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                ->findBy(
                    array(
                        'annee' => $anneeScolaire,
                        'classe' => $classe,
                    )
                );
            $listeMatieres = [];
            foreach ($listCategorie as $categorie) {
                foreach ($dispense as $enseign) {
                    if ($categorie == $enseign->getMatiere()->getCategorie()) {
                        $listeMatieres[] = $enseign->getMatiere();
                    }
                }
                foreach ($listeMatieres as $matiere) {
                    $matiere->setTaille(strlen($matiere->getNom()));
                    $evaluationSeq = $this->getDoctrine()->getRepository('SchoolNoteBundle:Evaluation')
                        ->findBy(
                            array(
                                'annee' => $anneeScolaire,
                                'student' => $eleve,
                                'sequence' => $sequence,
                                'matiere' => $matiere
                            )
                        );
                    foreach ($evaluationSeq as $note) {
                        $note->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                            ->findOneBy(array(
                                'annee' => $anneeScolaire,
                                'classe' => $classe,
                                'matiere' => $note->getMatiere()
                            )));
                        $matiere->setEvaluationSeq($evaluationSeq);
                    }
                    $categorie->setListeMatieres($listeMatieres);
                }
                $listeMatieres = [];
            }

            $titulaire = $this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                ->findOneBy(
                    array(
                        'annee' => $anneeScolaire,
                        'classe' => $classe,
                        'titulaire' => true
                    )
                )->getEnseignant();

            $html = $this->renderView('SchoolNoteBundle:Bulletin:bulletins.html.twig', array(
                'ecole' => $ecole,
                'pays' => $pays,
                'listCategories' => $listCategorie,
                'student' => $eleve,
                'annee' => $anneeScolaire,
                'Allstudent' => $Allstudent,
                'titulaire' => $titulaire,
                'classe' => $classe,
            ));

            $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
            $html2pdf->pdf->SetAuthor('GreenSoft');
            $html2pdf->pdf->SetTitle('Bulletin');
            $html2pdf->pdf->SetSubject('Bulletin Sequentiel');
            $html2pdf->pdf->SetKeywords('Classe, El?ve, Bulletin, Notes, S?quence');
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->writeHTML($html);

            $content = $html2pdf->Output('', true);
            $response = new Response();
            $response->setContent($content);
            //$response->headers->set('Content-Type', 'application/force-download');
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-disposition', 'filename=BulletinsSequentiels.pdf');
            return $response;
        } else {
            return $this->render('SchoolNoteBundle:Error:error1.html.twig');
        }
    }
    

    /**
     * Lists all evaluation entities.
     *
     * @Route("/performances/eleve-{idEleve}", name="bulletin_performance_eleve")
     * @Method("GET")
     */
    public function performancesAction($idEleve){
        $em = $this->getDoctrine()->getManager();
        $school = $em->getRepository('SchoolConfigBundle:Ecole')->findAll();
        $constante = $em->getRepository('SchoolConfigBundle:Constante')->findAll();
        $ecole = $school[0];
        $pays = $constante[0];
        $student = $em->getRepository('SchoolStudentBundle:Student')->find($idEleve);

        if (($student != NULL)) {/*Toutes les inscriptions de l'eleve au courant des ann? pass?dans l'etablissement*/
            $listeInscriptions = $this->getDoctrine()->getRepository('SchoolStudentBundle:Inscription')->findBy(array(
                'student' => $student,
            ));

            $listSequences = $this->getDoctrine()->getRepository('SchoolNoteBundle:Sequence')->findAll();
            $listCategories = $this->getDoctrine()->getRepository('SchoolMatiereBundle:Categorie')->findAll();

            $perfEleve = '<page backtop="10mm" backleft="10mm" backright="10mm" backbottom="10mm" footer="page;">
            <page_footer>
                <hr />
                <p>GreenSoft-Team</p>
            </page_footer>
               <table>
                <tr>
            <td class="40p">
                ' . $pays->getMinistereFrancais() . '<br/>
                ' . $ecole->getNomFrancais() . '<br/>
                ' . $ecole->getBoitePostal() . '
            </td>
            <td class="20p" style="text-align: center">
                <img style="height: 80px; width: 60px;" src="uploads/logos/' . $ecole->getLogo()->getId() . '.' . $ecole->getLogo()->getUrl() . '" alt="Logo" title="" >
            </td>
            <td style="text-align: right" class="40p">
                ' . $pays->getPaysFrancais() . '<br/>
                ' . $pays->getDeviseFrancais() . '<br/>
            </td>
        </tr>
            </table>
            <table class="info1" style="margin-top: 10px;">
                <tr>
                    <td class="25p" style="text-align: left;"></td>
                    <td class="50p" style="text-align: center; font-size: 1.2em"><strong>PERFORMANCES DE L\'ELEVE</strong></td>
                    <td class="25p" style="text-align: right;"></td>
                </tr>
            </table>';
            $perfEleve .= '
            <table class="info">
                <tr>
                    <td rowspan="2"  style="text-align: left; border-top: none" class="10p">';
            if ($student->getPhoto() != NULL) {
                $perfEleve .= '<img style="height: 90px;width: 80px;" src="uploads/images/' . $student->getPhoto()->getId() . '.' . $student->getPhoto()->getUrl() . '" alt="' . $student->getNom() . '" title="' . $student->getNom() . '">';
            }
            $perfEleve .= '
                    </td>
                    <td class="25p" style="text-align: left; border-top: none">El&egrave;ve: <b>' . $student->getNom() . '</b></td>
                    <td class="25p" style="text-align: left; border-top: none">
                        N&eacute;(e) le:<b>' . $student->getDateNaissance()->format('Y-m-d') . '</b><br> A <b> ' . $student->getLieuNaissance() . '</b>
                    </td>
                    <td class="15p" style="text-align: left; border-top:none; ">Matricule:  <b>' . $student->getMatricule() . '</b></td>
                    <td class="15p"  style="text-align: left; border-top: none">Sexe: <b>' . $student->getSexe() . '</b></td>
                    <td></td>
                </tr>
            </table>
            <table style="margin-top:10px;" class="notes">
        <tr>
            <th class="20p"></th>';
            foreach($listCategories as  $categorie ){
                $perfEleve .='<th style="text-align: center;" class="20p">' . $categorie->getNom() . '</th>';
            }
            $perfEleve .='
        </tr>';
            foreach ($listeInscriptions as $inscription) {
                $perfEleve .= '
                <tr>
                    <td>
                        ' . $inscription->getAnnee() . ' (' . $inscription->getClasse() . ')
                    </td>';
                $dispense = $this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')->findBy(
                    array(
                        'annee' => $inscription->getAnnee(),
                        'classe' => $inscription->getClasse(),
                    ));
                $listeMatieres = [];
                foreach ($listCategories as $categorie) {
                    foreach ($dispense as $enseign) {
                        if ($categorie == $enseign->getMatiere()->getCategorie()) {
                            $listeMatieres[] = $enseign->getMatiere();
                        }
                    }
                    $categorie->setListeMatieres($listeMatieres);
                    $listeMatieres = [];
                }
                foreach($listCategories as  $categorie ){
                    $perfEleve .='<td>';
                    $moySequentiel = 0;
                    $compteurSequences = 0;
                    foreach ($listSequences as $sequence) {
                        $totalCoefficient= 0;
                        $totalNoteParCategorie = 0;
                        foreach ($categorie->getListeMatieres() as $matiere) {
                            $matiere->setTaille(strlen($matiere->getNom()));
                            $evaluationSeq = $em->getRepository('SchoolNoteBundle:Evaluation')
                                ->findOneBy(
                                    array(
                                        'annee' => $inscription->getAnnee(),
                                        'student' => $student,
                                        'sequence' => $sequence,
                                        'matiere' => $matiere
                                    )
                                );
                            if ($evaluationSeq != NULL) {
                                $enseignement = $em->getRepository('SchoolMatiereBundle:EstDispense')
                                    ->findOneBy(array(
                                        'annee' => $inscription->getAnnee(),
                                        'classe' => $inscription->getClasse(),
                                        'matiere' => $evaluationSeq->getMatiere()
                                    ));
                                $totalCoefficient = $totalCoefficient + $enseignement->getCoefficient();
                                $totalNoteParCategorie = $totalNoteParCategorie + ($enseignement->getCoefficient()
                                        * $evaluationSeq->getNote());
                            }
                        }
                        if($totalCoefficient != 0 ){
                            $moySequentiel = $moySequentiel + ($totalNoteParCategorie/$totalCoefficient);
                            $compteurSequences = $compteurSequences+1;
                        }
                    }
                    if($compteurSequences){
                        $perfEleve .=
                            number_format($moySequentiel / $compteurSequences, 2, ',', ' ');
                        $perfEleve .= '</td>';
                    }else{
                        $perfEleve .=
                            '//';
                        $perfEleve .= '</td>';
                    }
                }

                $perfEleve .='
                </tr>
                ';
            }
            $perfEleve .='
        </table>
            </page>';

            $html = $this->renderView('SchoolNoteBundle:Bulletin:performancesEleve.html.twig', array(
                'inscriptions' => $listeInscriptions,
                'listCategories' => $listCategories,
                'listeSequences' => $listSequences,
                'performance' => $perfEleve,
            ));


            // $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
            $html2pdf = $this->get('html2pdf_factory')->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));
            $html2pdf->pdf->SetAuthor('GrenSoft-Team');
            $html2pdf->pdf->SetTitle('Performances' . ' ' . $student->getNom());
            $html2pdf->pdf->SetSubject('Performance  Eleve');
            $html2pdf->pdf->SetKeywords('Classe, Eleve, Bulletin, Notes, Sequence, Annee');
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->writeHTML($html);

            $content = $html2pdf->Output('', true);
            $response = new Response();
            $response->setContent($content);
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-disposition', 'filename=PerformancesEleve.pdf');
            return $response;
        } else {
            return $this->render('SchoolNoteBundle:Error:error1.html.twig');
        }
    }

    /**
     * Lists all evaluation entities.
     *
     * @Route("/classe-{idClasse}/sequence-{idSeq}/annee-{idAnnee}", name="bulletin_sequentiel_classe")
     * @Method("GET")
     */
    public function bulletinTestClasseAction($idSeq, $idAnnee, $idClasse) {
        $ecole = $this->getDoctrine()->getRepository('ConfigBundle:Constante')->find(1);
        $pays = $this->getDoctrine()->getRepository('ConfigBundle:Pays')->find(1);
        //$ecole = $school[0];
        //$pays = $constante[0];
        $classe = $this->getDoctrine()->getRepository('StudentBundle:Classe')->find($idClasse);
        $sequence = $this->getDoctrine()->getRepository('NoteBundle:Sequence')->find($idSeq);
        // $anneeScolaire = $this->getDoctrine()->getRepository('ConfigBundle:Annee')->find($idAnnee);
        $anneeScolaire = $this->getDoctrine()->getRepository('ConfigBundle:Annee')->findOneBy(['isAnneeEnCour' => true]);
        $matieresTotalClasse = $this->getDoctrine()->getRepository('MatiereBundle:EstDispense')->findBy(
            array(
                'annee' => $anneeScolaire,
                'classe' => $classe,
            )
        );
        $coefTotal = 0;
        foreach($matieresTotalClasse as $matiere){
            $coefTotal += $matiere->getCoefficient();
        }
        $AllstudentsteEleve = $this->getDoctrine()->getRepository('StudentBundle:Inscription')->findBy(
            array
            (
                'classe' => $classe,
                'annee' => $anneeScolaire
            )
        );
        $titulaire = $this->getDoctrine()->getRepository('MatiereBundle:EstDispense')->findOneBy(
            array(
                'annee' => $anneeScolaire,
                'classe' => $classe,
                'titulaire' => true
            )
        )->getEnseignant();
        if (($sequence != NULL) && ($anneeScolaire != NULL) && ($classe != NULL)) {
            $listCategorie = $this->getDoctrine()->getRepository('MatiereBundle:Categorie')->findAll();
            $dispense = $this->getDoctrine()->getRepository('MatiereBundle:EstDispense')->findBy(
                array(
                    'annee' => $anneeScolaire,
                    'classe' => $classe,
                ));
            $listeMatieres = [];
            foreach ($listCategorie as $categorie) {
                foreach ($dispense as $enseign) {
                    if ($categorie == $enseign->getMatiere()->getCategorie()) {
                        $listeMatieres[] = $enseign->getMatiere();
                    }
                }
                $categorie->setListeMatieres($listeMatieres);
                $listeMatieres = [];
            }
            $bullEleve = '';
            $tabMoy = [];
            foreach ($AllstudentsteEleve as $inscription) {
                $eleve = $inscription->getStudent();
                foreach ($listCategorie as $categorie) {
                    foreach ($categorie->getListeMatieres() as $matiere) {
                        $matiere->setTaille(strlen($matiere->getNom()));
                        $evaluationSeq = $this->getDoctrine()->getRepository('NoteBundle:Evaluation')
                            ->findOneBy(
                                array(
                                    'annee' => $anneeScolaire,
                                    'student' => $eleve,
                                    'sequence' => $sequence,
                                    'matiere' => $matiere
                                )
                            );
                        //foreach ($evaluationSeq as $note) {
                        if ($evaluationSeq != NULL) {
                            $evaluationSeq->setIndex($this->getDoctrine()->getRepository('MatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeq->getMatiere()
                                )));
                        }
                        $matiere->setEvaluationSeq($evaluationSeq);
                    }
                }
                $bullEleve .= '<page backtop="5mm" backleft="10mm" backright="10mm" backbottom="5mm" footer="page;">
            <page_footer>
                <p>GreenSoft-Team</p>
            </page_footer>
               <table>
                <tr>
            <td class="40p">
                ' . strtoupper($pays->getMinistereFrancais()) . '<br/>
                ' . strtoupper($ecole->getNomFrancais()) . '<br/>
                B.P.' . strtoupper($ecole->getBoitePostal()) . '
            </td>
            <td class="20p" style="text-align: center">';
			if($ecole->getLogo()){
               $bullEleve .= ' <img style="height: 80px; width: 60px;" src="uploads/logos/' . $ecole->getLogo()->getId() . '.' . $ecole->getLogo()->getUrl() . '" alt="Logo" title="" >';
				}
			$bullEleve .= '	
            </td>
            <td style="text-align: right" class="40p">
                ' . strtoupper($pays->getPaysFrancais()) . '<br/>
                ' . $pays->getDeviseFrancais() . '<br/>
                ' . $ecole->getDeviseFrancais() . '<br/>
            </td>
        </tr>
            </table>
            <table class="info1" style="margin-top: 10px;">
                <tr>
                    <td class="25p" style="text-align: left;"><strong>' . $anneeScolaire->getAnneeScolaire() . '</strong></td>
                    <td class="50p" style="text-align: center; font-size: 1.2em"><strong>BULLETIN DE NOTES</strong></td>
                    <td class="25p" style="text-align: right;"><strong>' . $sequence->getNom() . '</strong></td>
                </tr>
            </table>';
                $bullEleve .='
            <table class="info">
                <tr>
                    <td rowspan="2"  style="text-align: left; border-top: none" class="10p">';
                if ($eleve->getPhoto() != NULL) {
                    $bullEleve.= '<img style="height: 90px;width: 80px;" src="uploads/images/' . $eleve->getPhoto()->getId() . '.' . $eleve->getPhoto()->getUrl() . '" alt="' . $eleve->getNom() . '" title="' . $eleve->getNom() . '">';
                }
                $bullEleve .='
                    </td>
                    <td class="20p" style="text-align: left; border-top: none">El&egrave;ve: <b>' . strtoupper($eleve->getNom()) . '</b></td>
                    <td class="20p" style="text-align: left; border-top: none">
                        N&eacute;(e) le:<b>' . $eleve->getDateNaissance()->format('d-m-Y') . '</b><br> A <b> ' . $eleve->getLieuNaissance() . '</b>
                    </td>
                    <td class="15p" style="text-align: left; border-top:none; ">Matricule:  <b>'; if($eleve->getMatricule()){
						$bullEleve .= $eleve->getMatricule();
					}else{
						$bullEleve .='/';
					} 
					$bullEleve .='</b></td>
                    <td class="15p"  style="text-align: left; border-top: none">Sexe: <b>' . $eleve->getSexe() . '</b></td>
                </tr>
                <tr>
                    <td class="25p" style="text-align: left;" >Titulaire: <b>' . $titulaire->getNom() . '</b></td>
                    <td class="20p"  style="text-align: left">Classe:  <b>' . $inscription->getClasse()->getAbreviation() . '</b></td>
                    <td class="20p"  style="text-align: left">Effectif: <b>' . count($AllstudentsteEleve) . '</b></td>
                    <td class="20p"  style="text-align: left">Redoublant: ';
                if($inscription->getRedoublant()){
                    $bullEleve .='<b>OUI</b>';
                }else{
                    $bullEleve .='<b>NON</b>';
                }
                $bullEleve .='</td>
                </tr>
            </table>';
                $bullEleve .='
            <table class="notes" style="margin-top: 15px;" align="center">
                <tr>
                    <th class="20p" style="background: white;" >Disciplines</th>
                    <th class="20p" style="background: white;">Enseignants</th>
                    <th class="5p" style="background: white;">M./20</th>
                    <th class="5p" style="background: white;">Coef.</th>
                    <th class="10p" style="background: white;">Total</th>
                    <th class="15p" style="background: white;">Mention</th>
                </tr>';
                $somNote = $somCoef = 0;
                foreach ($listCategorie as $categorie) {
                    $somCoefCat = $somTotalCat = 0;
                    foreach ($categorie->getListeMatieres() as $matiere) {
                        $bullEleve .= '
                        <tr>
                            <td>';
                        if ($matiere->getTaille() < 13) {
                            $bullEleve .= $matiere->getNom();
                        } else {
                            $bullEleve .= $matiere->getAbreviation();
                        }
                        $bullEleve .= '
                            </td>
                            <td>';
                        if ($matiere->getEvaluationSeq() != NULL) {
                            $bullEleve .= $matiere->getEvaluationSeq()->getIndex()->getEnseignant();
                        }
                        $bullEleve .= '
                            </td>
                            <td style=" text-align:center">';
                        if ($matiere->getEvaluationSeq() != NULL) {
                            $note = $matiere->getEvaluationSeq()->getNote();
                            $bullEleve .= $matiere->getEvaluationSeq()->getNote();
                        }
                        $bullEleve .= '
                            </td>
                            <td style=" text-align:center">';
                        if ($matiere->getEvaluationSeq() != NULL) {
                            $somCoefCat = $somCoefCat + $matiere->getEvaluationSeq()->getIndex()->getCoefficient();
                            $somCoef = $somCoef + $matiere->getEvaluationSeq()->getIndex()->getCoefficient();
                            $bullEleve .= $matiere->getEvaluationSeq()->getIndex()->getCoefficient();
                        }
                        $bullEleve .= '
                            </td>
                            <td style=" text-align:center">';
                        if ($matiere->getEvaluationSeq() != NULL) {
                            $total = $matiere->getEvaluationSeq()->getIndex()->getCoefficient() * $matiere->getEvaluationSeq()->getNote();
                            $bullEleve .= $total;
                            $somTotalCat = $somTotalCat + $total;
                            $somNote = $somNote + $total;
                        }
                        $bullEleve .= '
                            </td>
                            <td>';
                        if ($matiere->getEvaluationSeq() != NULL) {
                            $bullEleve .= $this->getMention($note);
                        }
                        $bullEleve.='
                            </td>
                        </tr>';
                    }
                    $bullEleve .= '
                    <tr style="border: 2px solid black; background: wheat;">
                        <td  style="font-size: 14px; border-right: none">
                            <strong><i>' . $categorie->getNom() . '</i></strong>
                        </td>
                        <td style="border-right: none"></td>
                        <td style="border-left: none;"></td>
                        <td style=" text-align:center">
                            <b>' . $somCoefCat . '</b>
                        </td>
                        <td style=" text-align:center">
                            M:
                            <b>';
                    if ($somCoefCat > 0) {
                        $moyPart = number_format($somTotalCat / $somCoefCat, 2, '.', ' ');
                        $bullEleve .= number_format($somTotalCat / $somCoefCat, 2, ',', ' ');
                    } else {
                        $bullEleve .= '/';
                    }
                    $bullEleve .= '
                            </b>
                        </td>
                        <!--<td>
                            <b>Rang</b>
                        </td>-->
                        <td style="color: blue"><strong>';
                    if ($somCoefCat != 0) {
                        $bullEleve.= $this->getMention($moyPart);
                    }
                    $bullEleve.=
                        '</strong>
                        </td>
                    </tr>';
                }
                $abs = $this->getDoctrine()->getRepository('StudentBundle:Absence')
                    ->findOneBy(
                        array(
                            'student' => $inscription,
                            'anneeScolaire' => $anneeScolaire,
                            'sequence' => $sequence
                        )
                    );
                if ($abs == NULL) {
                    $absence = 0;
                } else {
                    $absence = $abs->getNbreAbsence();
                }
                $bullEleve.='
            </table>
            <table class="conduite" style="margin-top: 15px">
                <tr style="border: 2px solid black; font-size: 11px;">
                    <!--<td class="25p">
                        <b><u>Discipline</u>:<span style="font-size: 14px"></span></b>
                    </td>-->
                    <td class="50p">
                        <span style="font-size: 14px;"><u>CONDUITE</u></span><br>
                        <b><span style="font-size: 14px">Absences non justifi&eacute;es: ' . $absence . '</span></b>
                    </td>
                    <td class="25p">
                        <u>D&eacute;cision du conseil</u>:
                        ' . $this->decisionConseil($absence) . '
                    </td>
                    <td class="25p">
                        <u>Situation de l\'&eacute;l&egrave;ve:</u><<br/>
                        ' . $this->situation(($inscription)) . '
                    </td>
                </tr>
            </table>
            <table class="moyenne">
                <tr style="border: 2px solid black;">
                    <td class="20p">
                        <b style="color: blue;"><u>Moy. G&eacute;n. de la classe</u>: MOYENNE_GENERALE</b>
                    </td>
                    <td class="30p" style="color: green;">
                        <u>Moy. de l\'&eacute;l&egrave;ve</u>:<b> ';
                $moy = '';
                if ($somCoef > 0) {
                    $bullEleve.= number_format($somNote / $somCoef, 2, ',', ' ');
                    $moy = number_format($somNote / $somCoef, 2, '.', ' ');
                    if($somCoef >= ($coefTotal/2)){
                        $tabMoy[] = $moy;
                    }
                } else {
                    $bullEleve.='/';
                }
                // $bullEleve.=  count($tabMoy);
                $bullEleve.='
                        </b>
                        <br>
                        <b><u>Appr&eacute;ciation: </u> ' . $this->getMention($moy) . '</b>
                    </td>
                    <td class="10p">
                        <b style="color: orange"><u>RANG:</u>';
                if($somCoef >= ($coefTotal/2)){
                    $bullEleve .= 'RANG_'.$moy.'';
                }else{
                    $bullEleve .= '<span style="color: red;">NON CLASSE</span>';
                }
                $bullEleve.='</b>
                </td>
                    <td class="40p">
                        <b><u>OBSERVATIONS</u></b><br/>
                        <br/><br/><br/>
                    </td>
                </tr>
            </table>
            <table class="signature">
                <tr>
                    <td class="50p" style="border-bottom: none">
                        <u>Visa du parent</u><br/>
                        <br/><br/><br/>
                    </td>
                    <td class="50p" style="border-bottom: none">
                        Fait &agrave;  ' . $ecole->getVille() . ' le ' . date('d/m/Y') . '
                        <br/>
                        Le proviseur
                        <br/><br/><br/>
                    </td>
                </tr>
            </table>
            </page>';
            }

            if (count($tabMoy) != 0) {
                $moyGeneral = number_format(array_sum($tabMoy) / count($tabMoy), 2, ',', ' ');
            } else {
                $moyGeneral = '//';
            }
            $bullEleve = str_replace('MOYENNE_GENERALE', $moyGeneral, $bullEleve);
            $taille = count($tabMoy);
            sort($tabMoy);
            for ($i = $taille - 1, $j = 1; $i >= 0; $i--) {
                if ($j == 1) {
                    $bullEleve = str_replace('RANG_' . $tabMoy[$i], ($j++) . 'er(e)', $bullEleve);
                } else {
                    $bullEleve = str_replace('RANG_' . $tabMoy[$i], ($j++) . 'ème', $bullEleve);
                }
            }
            $html = $this->renderView('bulletin/bulletin_sequence_classe.html.twig', array(
                'ecole' => $ecole,
                'pays' => $pays,
                'annee' => $anneeScolaire,
                'Allstudent' => $AllstudentsteEleve,
                'titulaire' => $titulaire,
                'sequence' => $sequence,
                'page' => $bullEleve
            ));
            // $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
            $html2pdf = $this->get('html2pdf_factory')->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));
            $html2pdf->pdf->SetAuthor('GreenSoft-Team');
            $html2pdf->pdf->SetTitle('Bulletins' . ' ' . $sequence->getNom() . ' ' . $classe->getNom());
            $html2pdf->pdf->SetSubject('Bulletin Sequentiel');
            $html2pdf->pdf->SetKeywords('Classe, Eleve, Bulletin, Notes, Sequence');
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->writeHTML($html);
            $content = $html2pdf->Output('', true);
            $response = new Response();
            $response->setContent($content);
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-disposition', 'filename=Bulletins_'.$sequence->getNom().'_'.$classe->getNom().'.pdf');
            return $response;
        } else {
            return $this->render('SchoolNoteBundle:Error:error1.html.twig');
        }
    }
    
    /**
     * Lists all evaluation entities.
     *
     * @Route("/classe-{idClasse}/annee-{idAnnee}/{trim}", name="bulletin_trimestriel_classe")
     * @Method("GET")
     */
    public function bulletinTrimestrielleClasseAction($idAnnee, $idClasse, $trim) {
        $em = $this->getDoctrine()->getManager();

        if($trim == 'Trim1'){
            $sequenceFirst = $em->getRepository('SchoolNoteBundle:Sequence')->find(1);
            $sequenceSecond = $em->getRepository('SchoolNoteBundle:Sequence')->find(2);
            $valeurTrim = 'Trimestre 1';
        }else if($trim == 'Trim2'){
            $sequenceFirst = $em->getRepository('SchoolNoteBundle:Sequence')->find(3);
            $sequenceSecond = $em->getRepository('SchoolNoteBundle:Sequence')->find(4);
            $valeurTrim = 'Trimestre 2';
        }

        $school = $em->getRepository('SchoolConfigBundle:Ecole')->findAll();
        $constante = $em->getRepository('SchoolConfigBundle:Constante')->findAll();
        $ecole = $school[0];
        $pays = $constante[0];
        $classe = $em->getRepository('SchoolStudentBundle:Classe')->find($idClasse);
        $anneeScolaire = $em->getRepository('SchoolConfigBundle:Annee')->find($idAnnee);
        $matieresTotalClasse = $em->getRepository('SchoolMatiereBundle:EstDispense')->findBy(
            array(
                'annee' => $anneeScolaire,
                'classe' => $classe,
            )
        );
        $coefTotal = 0;
        foreach($matieresTotalClasse as $matiere){
            $coefTotal += $matiere->getCoefficient();
        }
        $AllstudentsteEleve = $this->getDoctrine()->getRepository('SchoolStudentBundle:Inscription')->findBy(
            array
            (
                'classe' => $classe,
                'annee' => $anneeScolaire
            )
        );
        $titulaire = $this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')->findOneBy(
            array(
                'annee' => $anneeScolaire,
                'classe' => $classe,
                'titulaire' => true
            )
        )->getEnseignant();

        if (($sequenceFirst != NULL) && ($sequenceSecond != NULL) && ($anneeScolaire != NULL) && ($classe != NULL)) {
            $listCategorie = $this->getDoctrine()->getRepository('SchoolMatiereBundle:Categorie')->findAll();
            $dispense = $this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')->findBy(
                array(
                    'annee' => $anneeScolaire,
                    'classe' => $classe,
                ));
            $listeMatieres = [];
            foreach ($listCategorie as $categorie) {
                foreach ($dispense as $enseign) {
                    if ($categorie == $enseign->getMatiere()->getCategorie()) {
                        $listeMatieres[] = $enseign->getMatiere();
                    }
                }
                $categorie->setListeMatieres($listeMatieres);
                $listeMatieres = [];
            }
            $bullEleve = '';
            $tabMoy = [];
            foreach ($AllstudentsteEleve as $inscription) {
                $eleve = $inscription->getStudent();
                foreach ($listCategorie as $categorie) {
                    foreach ($categorie->getListeMatieres() as $matiere) {
                        $matiere->setTaille(strlen($matiere->getNom()));
                        $evaluationSeqFirst = $this->getDoctrine()->getRepository('SchoolNoteBundle:Evaluation')
                            ->findOneBy(
                                array(
                                    'annee' => $anneeScolaire,
                                    'student' => $eleve,
                                    'sequence' => $sequenceFirst,
                                    'matiere' => $matiere
                                )
                            );
                        $evaluationSeqSecond= $this->getDoctrine()->getRepository('SchoolNoteBundle:Evaluation')
                            ->findOneBy(
                                array(
                                    'annee' => $anneeScolaire,
                                    'student' => $eleve,
                                    'sequence' => $sequenceSecond,
                                    'matiere' => $matiere
                                )
                            );
                        if ($evaluationSeqFirst != NULL && $evaluationSeqSecond != NULL ) {
                            $evaluationSeqFirst->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqFirst->getMatiere()
                                )));
                            $evaluationSeqSecond->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqSecond->getMatiere()
                                )));
                        }else if($evaluationSeqFirst != NULL){
                            $evaluationSeqFirst->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqFirst->getMatiere()
                                )));
                        }else if($evaluationSeqSecond != NULL){
                            $evaluationSeqSecond->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqSecond->getMatiere()
                                )));
                        }
                        $matiere->setEvaluationSeq($evaluationSeqFirst);
                        $matiere->setEvaluationSeq1($evaluationSeqSecond);
                    }
                }
                $bullEleve .= '<page backtop="5mm" backleft="10mm" backright="10mm" backbottom="5mm" footer="page;">
            <page_footer>
                <p>GreenSoft-Team</p>
            </page_footer>
               <table>
                <tr>
            <td class="40p">
                ' . strtoupper($pays->getMinistereFrancais()) . '<br/>
                ' . strtoupper($ecole->getNomFrancais()) . '<br/>
                B.P. ' . strtoupper($ecole->getBoitePostal()) . '
            </td>
            <td class="20p" style="text-align: center">';
			if($ecole->getLogo()){
					$bullEleve .= '<img style="height: 80px; width: 60px;" src="uploads/logos/' . $ecole->getLogo()->getId() . '.' . $ecole->getLogo()->getUrl() . '" alt="Logo" title="" >';
			}
			$bullEleve .= '
            </td>
            <td style="text-align: right" class="40p">
                ' . strtoupper($pays->getPaysFrancais()) . '<br/>
                ' . $pays->getDeviseFrancais() . '<br/>
                ' . $ecole->getDeviseFrancais() . '<br/>
            </td>
        </tr>
            </table>
            <table class="info1" style="margin-top: 10px;">
                <tr>
                    <td class="25p" style="text-align: left;"><strong>' . $anneeScolaire->getAnneeScolaire() . '</strong></td>';
                if($trim == 'Trim1') {
                    $bullEleve .= '<td class="50p" style="text-align: center; font-size: 1.2em"><strong>BULLETIN DE NOTES DU PREMIER TRIMESTRE </strong></td>';
                } else if($trim == 'Trim2'){
                    $bullEleve .= '<td class="50p" style="text-align: center; font-size: 1.2em"><strong>BULLETIN DE NOTES DU DEUXIEME TRIMESTRE </strong></td>';
                }else{
                    $bullEleve .= '<td class="50p" style="text-align: center; font-size: 1.2em"><strong>BULLETIN DE NOTES DU TROIXIEME TRIMESTRE </strong></td>';
                }
                $bullEleve .=' <td class="25p" style="text-align: right;"><strong></strong></td>
                </tr>
            </table>';
                $bullEleve .='
            <table class="info">
                <tr>
                    <td rowspan="2"  style="text-align: left; border-top: none" class="10p">';
                if ($eleve->getPhoto() != NULL) {
                    $bullEleve.= '<img style="height: 90px;width: 80px;" src="uploads/images/' . $eleve->getPhoto()->getId() . '.' . $eleve->getPhoto()->getUrl() . '" alt="' . $eleve->getNom() . '" title="' . $eleve->getNom() . '">';
                }
                $bullEleve .='
                    </td>
                    <td class="20p" style="text-align: left; border-top: none">El&egrave;ve: <b>' . strtoupper($eleve->getNom()) . '</b></td>
                    <td class="20p" style="text-align: left; border-top: none">
                        N&eacute;(e) le:<b>' . $eleve->getDateNaissance()->format('d-m-Y') . '</b><br> A <b> ' . $eleve->getLieuNaissance() . '</b>
                    </td>
                    <td class="15p" style="text-align: left; border-top:none; ">Matricule:  <b>' . $eleve->getMatricule() . '</b></td>
                    <td class="15p"  style="text-align: left; border-top: none">Sexe: <b>' . $eleve->getSexe() . '</b></td>
                </tr>
                <tr>
                    <td class="25p" style="text-align: left;" >Titulaire: <b>' . $titulaire->getNom() . '</b></td>
                    <td class="20p"  style="text-align: left">Classe:  <b>' . $inscription->getClasse()->getAbreviation() . '</b></td>
                    <td class="20p"  style="text-align: left">Effectif: <b>' . count($AllstudentsteEleve) . '</b></td>
                   <td class="20p"  style="text-align: left">Redoublant(e): ';
                if($inscription->getRedoublant()){
                    $bullEleve .='<b>OUI</b>';
                }else{
                    $bullEleve .='<b>NON</b>';
                }
                $bullEleve .='</td>
                </tr>
            </table>';
                $bullEleve .='
            <table class="notes" style="margin-top: 15px;" align="center">
                <tr>
                    <th class="20p" style="background: white;" >Disciplines</th>
                    <th class="20p" style="background: white;">Enseignants</th>';
                if($trim == 'Trim1'){
                    $bullEleve .='
                            <th class="5p" style="background: white;">Seq 1</th>
                            <th class="5p" style="background: white;">Seq 2</th>
                            <th class="5p" style="background: white;">M./20</th>
                            ';
                }else if($trim == 'Trim2'){
                    $bullEleve .='
                            <th class="5p" style="background: white;">Seq 3</th>
                            <th class="5p" style="background: white;">Seq 4</th>
                            <th class="5p" style="background: white;">M./20</th>
                            ';
                }else{
                    $bullEleve .='
                            <th class="5p" style="background: white;">Seq 5</th>
                            <th class="5p" style="background: white;">Seq 6</th>
                            <th class="5p" style="background: white;">M./20</th>
                            ';
                }
                $bullEleve .='
                    <th class="5p" style="background: white;">Coef.</th>
                    <th class="10p" style="background: white;">Total</th>
                    <!--<th class="5p" style="background: white;">Rang</th>-->
                    <th class="15p" style="background: white;">Mention</th>
                </tr>';
                $somNote = $somCoef = 0;
                foreach ($listCategorie as $categorie) {
                    $somCoefCat = $somTotalCat = 0;
                    foreach ($categorie->getListeMatieres() as $matiere) {
                        $bullEleve .= '
                        <tr>
                            <td>';
                        if ($matiere->getTaille() > 13) {
                            $bullEleve .= $matiere->getAbreviation();
                        } else {
                            $bullEleve .= $matiere->getNom();
                        }
                        $bullEleve .= '
                            </td>
                            <td>';
                        if ($matiere->getEvaluationSeq() != NULL || $matiere->getEvaluationSeq1() != NULL) {
                            if($matiere->getEvaluationSeq() != NULL ){
                                $bullEleve .= $matiere->getEvaluationSeq()->getIndex()->getEnseignant();
                            }else{
                                $bullEleve .= $matiere->getEvaluationSeq1()->getIndex()->getEnseignant();
                            }
                        }
                        $bullEleve .= '
                            </td>
                            <td style="text-align: center">';
                        if ($matiere->getEvaluationSeq() != NULL) {
                            $bullEleve .= $matiere->getEvaluationSeq()->getNote();
                        }else{
                            $bullEleve.='/';
                        }
                        $bullEleve .= '
                            </td>
                            <td style="text-align: center">';
                        if ($matiere->getEvaluationSeq1() != NULL) {
                            $bullEleve .= $matiere->getEvaluationSeq1()->getNote();
                        }else{
                            $bullEleve.='/';
                        }
                        $bullEleve .= '
                            </td>
                            <td style="text-align: center">';
                        if ($matiere->getEvaluationSeq() != NULL && $matiere->getEvaluationSeq1() != NULL) {
                            $note = ($matiere->getEvaluationSeq()->getNote() + $matiere->getEvaluationSeq1()->getNote())/2;
                            //$bullEleve .= $matiere->getEvaluationSeq()->getNote();
                            $bullEleve .= $note;
                        }else if($matiere->getEvaluationSeq() != NULL){
                            $note = $matiere->getEvaluationSeq()->getNote();
                            $bullEleve .= $note;
                        }else if($matiere->getEvaluationSeq1() != NULL){
                            $note = $matiere->getEvaluationSeq1()->getNote();
                            $bullEleve .= $note;
                        }
                        $bullEleve .= '
                            </td>
                            <td style="text-align: center">';
                        if($matiere->getEvaluationSeq() != NULL || $matiere->getEvaluationSeq1() != NULL) {
                            if($matiere->getEvaluationSeq() != NULL){
                                $somCoefCat = $somCoefCat + $matiere->getEvaluationSeq()->getIndex()->getCoefficient();
                                $somCoef = $somCoef + $matiere->getEvaluationSeq()->getIndex()->getCoefficient();
                                $bullEleve .= $matiere->getEvaluationSeq()->getIndex()->getCoefficient();
                            }else{
                                $somCoefCat = $somCoefCat + $matiere->getEvaluationSeq1()->getIndex()->getCoefficient();
                                $somCoef = $somCoef + $matiere->getEvaluationSeq1()->getIndex()->getCoefficient();
                                $bullEleve .= $matiere->getEvaluationSeq1()->getIndex()->getCoefficient();
                            }
                        }
                        $bullEleve .= '
                            </td>
                            <td style="text-align: center">';
                        if ($matiere->getEvaluationSeq() != NULL ||  $matiere->getEvaluationSeq1() != NULL) {
                            if($matiere->getEvaluationSeq() != NULL){
                                $total = $matiere->getEvaluationSeq()->getIndex()->getCoefficient() * $note;
                                $bullEleve .= $total;
                                $somTotalCat = $somTotalCat + $total;
                                $somNote = $somNote + $total;
                            }else{
                                $total = $matiere->getEvaluationSeq1()->getIndex()->getCoefficient() * $note;
                                $bullEleve .= $total;
                                $somTotalCat = $somTotalCat + $total;
                                $somNote = $somNote + $total;
                            }
                        }
                        $bullEleve .= '
                            </td>
                            <!--<td>
                                Rang
                            </td>-->
                            <td>';
                        if ($matiere->getEvaluationSeq() != NULL || $matiere->getEvaluationSeq1() != NULL) {
                            $bullEleve .= $this->getMention($note);
                        }
                        $bullEleve.='
                            </td>
                        </tr>';
                    }
                    $bullEleve .= '
                    <tr style="border: 2px solid black; background: wheat;">
                        <td  style="font-size: 14px; border-right: none">
                            <strong><i>' . $categorie->getNom() . '</i></strong>
                        </td>
                        <td style="border-right: none"></td>
                        <td style="border-right: none"></td>
                        <td style="border-right: none"></td>
                        <td style="border-left: none"></td>
                        <td style=" text-align:center">
                            <b>' . $somCoefCat . '</b>
                        </td>
                        <td style="text-align:center">
                            M:
                            <b>';
                    if ($somCoefCat > 0) {
                        $moyPart = number_format($somTotalCat / $somCoefCat, 2, '.', ' ');
                        $bullEleve .= number_format($somTotalCat / $somCoefCat, 2, ',', ' ');
                    } else {
                        $bullEleve .= '/';
                    }
                    $bullEleve .= '
                            </b>
                        </td>
                       <!-- <td>
                            <b>Rang</b>
                        </td>-->
                        <td style="color: blue"><strong>';
                    if ($somCoefCat != 0) {
                        $bullEleve.= $this->getMention($moyPart);
                    }
                    $bullEleve.=
                        '</strong>
                        </td>
                    </tr>';
                }
                $abs = $this->getDoctrine()->getRepository('SchoolStudentBundle:Absence')
                    ->findOneBy(
                        array(
                            'student' => $inscription,
                            'anneeScolaire' => $anneeScolaire,
                            'sequence' => $sequenceFirst
                        )
                    );
                $abs1 = $this->getDoctrine()->getRepository('SchoolStudentBundle:Absence')
                    ->findOneBy(
                        array(
                            'student' => $inscription,
                            'anneeScolaire' => $anneeScolaire,
                            'sequence' => $sequenceSecond
                        )
                    );

                if ($abs == NULL && $abs1 == NULL) {
                    $absence = 0;
                }else if($abs != NULL && $abs1 == NULL) {
                    $absence = $abs->getNbreAbsence();
                }else if($abs == NULL && $abs1 != NULL) {
                    $absence = $abs1->getNbreAbsence();
                }else{
                    $absence = $abs1->getNbreAbsence() + $abs->getNbreAbsence();
                }
                $bullEleve.='
            </table>
            <table class="conduite" style="margin-top: 15px">
                <tr style="border: 2px solid black; font-size: 11px;">
                    <!--<td class="25p">
                        <b><u>Discipline</u>:<span style="font-size: 14px"></span></b>
                    </td>-->
                    <td class="50p">
                        <span style="font-size: 14px;"><u>CONDUITE</u></span><br>
                        <b><span style="font-size: 14px">Absences non justifi&eacute;es: ' . $absence . '</span></b>
                    </td>
                    <td class="25p">
                        <u>D&eacute;cision du conseil</u>:
                        ' . $this->decisionConseil($absence) . '
                    </td>
                    <td class="25p">
                        <u>Situation de l\'&eacute;l&egrave;ve:</u><<br/>
                        ' . $this->situation(($inscription)) . '
                    </td>
                </tr>
            </table>
            <table class="moyenne">
                <tr style="border: 2px solid black;">
                    <td class="20p">
                        <b style="color: blue;"><u>Moy. Gen. de la classe</u>: MOYENNE_GENERALE</b>
                    </td>
                    <td class="30p" style="color: green;">
                        <u>Moy. de l\'&eacute;l&egrave;ve</u>:<b> ';
                $moy = '';
                if ($somCoef > 0) {
                    $bullEleve.= number_format($somNote / $somCoef, 2, ',', ' ');
                    $moy = number_format($somNote / $somCoef, 2, '.', ' ');
                    if($somCoef >= ($coefTotal/2)){
                        $tabMoy[] = $moy;
                    }
                } else {
                    $bullEleve.='/';
                }
                $bullEleve.='
                        </b>
                        <br>
                        <b><u>Appr&eacute;ciation: </u> ' . $this->getMention($moy) . '</b>
                    </td>
                    <td class="10p">
                        <b style="color: orange"><u>RANG:</u>';
                if($somCoef >= ($coefTotal/2)){
                    $bullEleve .= 'RANG_'.$moy.'';
                }else{
                    $bullEleve .= '<span style="color: red;">NON CLASSE</span>';
                }
                $bullEleve.='</b>
                </td>
                    <td class="40p" >
                        <b><u>OBSERVATIONS</u></b><br/>
                        <br/><br/><br/>
                    </td>
                </tr>
            </table>
            <table class="signature">
                <tr>
                    <td class="50p" style="border-bottom: none">
                        <u>Visa du parent</u><br/>
                        <br/><br/><br/>
                    </td>
                    <td class="50p" style="border-bottom: none">
                        Fait &agrave;  ' . $ecole->getVille() . ' le ' . date('d/m/Y') . '
                        <br/>
                        Le proviseur
                        <br/><br/><br/>
                    </td>
                </tr>
            </table>
            </page>';
            }

            if (count($tabMoy) != 0) {
                $moyGeneral = number_format(array_sum($tabMoy) / count($tabMoy), 2, ',', ' ');
            } else {
                $moyGeneral = '//';
            }
            $bullEleve = str_replace('MOYENNE_GENERALE', $moyGeneral, $bullEleve);
            $taille = count($tabMoy);
            sort($tabMoy);
            for ($i = $taille - 1, $j = 1; $i >= 0; $i--) {
                if ($j == 1) {
                    $bullEleve = str_replace('RANG_' . $tabMoy[$i], ($j++) . 'er(e)', $bullEleve);
                } else {
                    $bullEleve = str_replace('RANG_' . $tabMoy[$i], ($j++) . 'ème', $bullEleve);
                }
            }
            $html = $this->renderView('SchoolNoteBundle:Bulletin:bulletinTrimClasse.html.twig', array(
                'ecole' => $ecole,
                'pays' => $pays,
                'annee' => $anneeScolaire,
                'Allstudent' => $AllstudentsteEleve,
                'titulaire' => $titulaire,
                //'sequence' => $sequence,
                'page' => $bullEleve
            ));
            $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
            $html2pdf->pdf->SetAuthor('GreenSoft-Team');
            $html2pdf->pdf->SetTitle('Bulletins' .  'Trimestriel ' . $classe->getNom());
            $html2pdf->pdf->SetSubject('Bulletin Sequentiel');
            $html2pdf->pdf->SetKeywords('Classe, Eleve, Bulletin, Notes, Trimestre');
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->writeHTML($html);
            $content = $html2pdf->Output('', true);
            $response = new Response();
            $response->setContent($content);
            //$response->headers->set('Content-Type', 'application/force-download');
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-disposition', 'filename=Bulletins_'.$classe->getNom().'_'.$valeurTrim.'.pdf');
            return $response;
        } else {
            return $this->render('SchoolNoteBundle:Error:error1.html.twig');
        }
    }
    
    /**
     * Lists all evaluation entities.
     *
     * @Route("/classe-{idClasse}/annee-{idAnnee}", name="bulletin_trimestriel3_classe")
     * @Method("GET")
     */
    public function bulletinTrim3ClasseAction($idAnnee, $idClasse) {
        $em = $this->getDoctrine()->getManager();


        $sequenceFirst = $em->getRepository('SchoolNoteBundle:Sequence')->find(5);
        $sequenceSecond = $em->getRepository('SchoolNoteBundle:Sequence')->find(6);

        /*Deux séquences du premier trimestre*/
        $sequenceOne = $em->getRepository('SchoolNoteBundle:Sequence')->find(1);
        $sequenceTwo= $em->getRepository('SchoolNoteBundle:Sequence')->find(2);

        /*Deux séquences du deuxième trimestre*/
        $sequenceThree= $em->getRepository('SchoolNoteBundle:Sequence')->find(3);
        $sequenceFourth= $em->getRepository('SchoolNoteBundle:Sequence')->find(4);

        $school = $em->getRepository('SchoolConfigBundle:Ecole')->findAll();
        $constante = $em->getRepository('SchoolConfigBundle:Constante')->findAll();
        $ecole = $school[0];
        $pays = $constante[0];
        $classe = $em->getRepository('SchoolStudentBundle:Classe')->find($idClasse);
        $anneeScolaire = $em->getRepository('SchoolConfigBundle:Annee')->find($idAnnee);
        $matieresTotalClasse = $em->getRepository('SchoolMatiereBundle:EstDispense')->findBy(
            array(
                'annee' => $anneeScolaire,
                'classe' => $classe,
            )
        );
        $coefTotal = $coefTotalTrim1 = $coefTotalTrim2 = 0;
        foreach($matieresTotalClasse as $matiere){
            $coefTotal += $matiere->getCoefficient();
            $coefTotalTrim1 += $matiere->getCoefficient();
            $coefTotalTrim2 += $matiere->getCoefficient();
        }
        $AllstudentsteEleve = $this->getDoctrine()->getRepository('SchoolStudentBundle:Inscription')->findBy(
            array
            (
                'classe' => $classe,
                'annee' => $anneeScolaire
            )
        );
        $titulaire = $this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')->findOneBy(
            array(
                'annee' => $anneeScolaire,
                'classe' => $classe,
                'titulaire' => true
            )
        )->getEnseignant();

        if (($sequenceFirst != NULL) && ($sequenceSecond != NULL) && ($anneeScolaire != NULL) && ($classe != NULL)) {
            $listCategorie = $this->getDoctrine()->getRepository('SchoolMatiereBundle:Categorie')->findAll();
            $dispense = $this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')->findBy(
                array(
                    'annee' => $anneeScolaire,
                    'classe' => $classe,
                ));
            $listeMatieres = [];
            foreach ($listCategorie as $categorie) {
                foreach ($dispense as $enseign) {
                    if ($categorie == $enseign->getMatiere()->getCategorie()) {
                        $listeMatieres[] = $enseign->getMatiere();
                    }
                }
                $categorie->setListeMatieres($listeMatieres);
                $listeMatieres = [];
            }
            $bullEleve = '';
            $tabMoy = [];
            $tabMoyenneTrim = [];
            $tabMoyenneAnnuelle = [];
            foreach ($AllstudentsteEleve as $inscription) {
                $eleve = $inscription->getStudent();
                foreach ($listCategorie as $categorie) {
                    foreach ($categorie->getListeMatieres() as $matiere) {
                        $matiere->setTaille(strlen($matiere->getNom()));
                        $evaluationSeqFirst = $this->getDoctrine()->getRepository('SchoolNoteBundle:Evaluation')
                            ->findOneBy(
                                array(
                                    'annee' => $anneeScolaire,
                                    'student' => $eleve,
                                    'sequence' => $sequenceFirst,
                                    'matiere' => $matiere
                                )
                            );
                        $evaluationSeqSecond= $this->getDoctrine()->getRepository('SchoolNoteBundle:Evaluation')
                            ->findOneBy(
                                array(
                                    'annee' => $anneeScolaire,
                                    'student' => $eleve,
                                    'sequence' => $sequenceSecond,
                                    'matiere' => $matiere
                                )
                            );
                        if ($evaluationSeqFirst != NULL && $evaluationSeqSecond != NULL ) {
                            $evaluationSeqFirst->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqFirst->getMatiere()
                                )));
                            $evaluationSeqSecond->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqSecond->getMatiere()
                                )));
                        }else if($evaluationSeqFirst != NULL){
                            $evaluationSeqFirst->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqFirst->getMatiere()
                                )));
                        }else if($evaluationSeqSecond != NULL){
                            $evaluationSeqSecond->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqSecond->getMatiere()
                                )));
                        }
                        $evaluationSeqOne= $this->getDoctrine()->getRepository('SchoolNoteBundle:Evaluation')
                            ->findOneBy(
                                array(
                                    'annee' => $anneeScolaire,
                                    'student' => $eleve,
                                    'sequence' => $sequenceOne,
                                    'matiere' => $matiere
                                )
                            );
                        $evaluationSeqTwo= $this->getDoctrine()->getRepository('SchoolNoteBundle:Evaluation')
                            ->findOneBy(
                                array(
                                    'annee' => $anneeScolaire,
                                    'student' => $eleve,
                                    'sequence' => $sequenceTwo,
                                    'matiere' => $matiere
                                )
                            );
                        $evaluationSeqThree= $this->getDoctrine()->getRepository('SchoolNoteBundle:Evaluation')
                            ->findOneBy(
                                array(
                                    'annee' => $anneeScolaire,
                                    'student' => $eleve,
                                    'sequence' => $sequenceThree,
                                    'matiere' => $matiere
                                )
                            );

                        $evaluationSeqFourth = $this->getDoctrine()->getRepository('SchoolNoteBundle:Evaluation')
                            ->findOneBy(
                                array(
                                    'annee' => $anneeScolaire,
                                    'student' => $eleve,
                                    'sequence' => $sequenceFourth,
                                    'matiere' => $matiere
                                )
                            );

                        if ($evaluationSeqOne != NULL && $evaluationSeqTwo != NULL ) {
                            $evaluationSeqOne->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqOne->getMatiere()
                                )));
                            $evaluationSeqTwo->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqTwo->getMatiere()
                                )));
                        }else if($evaluationSeqOne != NULL){
                            $evaluationSeqOne->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqOne->getMatiere()
                                )));
                        }else if($evaluationSeqTwo != NULL){
                            $evaluationSeqTwo->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqTwo->getMatiere()
                                )));
                        }
                        if ($evaluationSeqThree != NULL && $evaluationSeqFourth != NULL ) {
                            $evaluationSeqThree->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqThree->getMatiere()
                                )));
                            $evaluationSeqFourth->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqFourth->getMatiere()
                                )));
                        }else if($evaluationSeqThree != NULL){
                            $evaluationSeqThree->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqThree->getMatiere()
                                )));
                        }else if($evaluationSeqFourth != NULL){
                            $evaluationSeqFourth->setIndex($this->getDoctrine()->getRepository('SchoolMatiereBundle:EstDispense')
                                ->findOneBy(array(
                                    'annee' => $anneeScolaire,
                                    'classe' => $classe,
                                    'matiere' => $evaluationSeqFourth->getMatiere()
                                )));
                        }
                        $matiere->setEvaluationSeq($evaluationSeqFirst);
                        $matiere->setEvaluationSeq1($evaluationSeqSecond);
                        $matiere->setEvaluationSeqOne($evaluationSeqOne);
                        $matiere->setEvaluationSeqTwo($evaluationSeqTwo);
                        $matiere->setEvaluationSeqThree($evaluationSeqThree);
                        $matiere->setEvaluationSeqFourth($evaluationSeqFourth);
                    }
                }
                $bullEleve .= '<page backtop="5mm" backleft="10mm" backright="10mm" backbottom="5mm" footer="page;">
            <page_footer>
                <p>GreenSoft-Team</p>
            </page_footer>
               <table>
                <tr>
            <td class="40p">
                ' . strtoupper($pays->getMinistereFrancais()) . '<br/>
                ' . strtoupper($ecole->getNomFrancais()) . '<br/>
                B.P. ' . strtoupper($ecole->getBoitePostal()) . '
            </td>
            <td class="20p" style="text-align: center">';
                if($ecole->getLogo()){
                    $bullEleve .= '<img style="height: 80px; width: 60px;" src="uploads/logos/' . $ecole->getLogo()->getId() . '.' . $ecole->getLogo()->getUrl() . '" alt="Logo" title="" >';
                }
                $bullEleve .= '
            </td>
            <td style="text-align: right" class="40p">
                ' . strtoupper($pays->getPaysFrancais()) . '<br/>
                ' . $pays->getDeviseFrancais() . '<br/>
                ' . $ecole->getDeviseFrancais() . '<br/>
            </td>
        </tr>
            </table>
            <table class="info1" style="margin-top: 10px;">
                <tr>
                    <td class="25p" style="text-align: left;"><strong>' . $anneeScolaire->getAnneeScolaire() . '</strong></td>';
                    $bullEleve .= '<td class="50p" style="text-align: center; font-size: 1.2em"><strong>BULLETIN DE NOTES DU TROIXIEME TRIMESTRE </strong></td>';
                $bullEleve .=' <td class="25p" style="text-align: right;"><strong></strong></td>
                </tr>
            </table>';
                $bullEleve .='
            <table class="info">
                <tr>
                    <td rowspan="2"  style="text-align: left; border-top: none" class="10p">';
                if ($eleve->getPhoto() != NULL) {
                    $bullEleve.= '<img style="height: 90px;width: 80px;" src="uploads/images/' . $eleve->getPhoto()->getId() . '.' . $eleve->getPhoto()->getUrl() . '" alt="' . $eleve->getNom() . '" title="' . $eleve->getNom() . '">';
                }
                $bullEleve .='
                    </td>
                    <td class="20p" style="text-align: left; border-top: none">El&egrave;ve: <b>' . strtoupper($eleve->getNom()) . '</b></td>
                    <td class="20p" style="text-align: left; border-top: none">
                        N&eacute;(e) le:<b>' . $eleve->getDateNaissance()->format('d-m-Y') . '</b><br> A <b> ' . $eleve->getLieuNaissance() . '</b>
                    </td>
                    <td class="15p" style="text-align: left; border-top:none; ">Matricule:  <b>' . $eleve->getMatricule() . '</b></td>
                    <td class="15p"  style="text-align: left; border-top: none">Sexe: <b>' . $eleve->getSexe() . '</b></td>
                </tr>
                <tr>
                    <td class="25p" style="text-align: left;" >Titulaire: <b>' . $titulaire->getNom() . '</b></td>
                    <td class="20p"  style="text-align: left">Classe:  <b>' . $inscription->getClasse()->getAbreviation() . '</b></td>
                    <td class="20p"  style="text-align: left">Effectif: <b>' . count($AllstudentsteEleve) . '</b></td>
                   <td class="20p"  style="text-align: left">Redoublant(e): ';
                if($inscription->getRedoublant()){
                    $bullEleve .='<b>OUI</b>';
                }else{
                    $bullEleve .='<b>NON</b>';
                }
                $bullEleve .='</td>
                </tr>
            </table>';
                $bullEleve .='
            <table class="notes" style="margin-top: 15px;" align="center">
                <tr>
                    <th class="20p" style="background: white;" >Disciplines</th>
                    <th class="20p" style="background: white;">Enseignants</th>';

                    $bullEleve .='
                            <th class="5p" style="background: white;">Seq 5</th>
                            <th class="5p" style="background: white;">Seq 6</th>
                            <th class="5p" style="background: white;">M./20</th>
                            ';
                $bullEleve .='
                    <th class="5p" style="background: white;">Coef.</th>
                    <th class="10p" style="background: white;">Total</th>
                    <!--<th class="5p" style="background: white;">Rang</th>-->
                    <th class="15p" style="background: white;">Mention</th>
                </tr>';
                $somNote = $somCoef = $somCoefTrim1 = $somCoefTrim2 = $somNoteTrim1 = $somNoteTrim2 = $totalTrim1 = $totalTrim2 = 0;
                foreach ($listCategorie as $categorie) {
                    $somCoefCat = $somTotalCat = 0;
                    foreach ($categorie->getListeMatieres() as $matiere) {
                        $bullEleve .= '
                        <tr>
                            <td>';
                        if ($matiere->getTaille() > 13) {
                            $bullEleve .= $matiere->getAbreviation();
                        } else {
                            $bullEleve .= $matiere->getNom();
                        }
                        $bullEleve .= '
                            </td>
                            <td>';
                        if ($matiere->getEvaluationSeq() != NULL || $matiere->getEvaluationSeq1() != NULL) {
                            if($matiere->getEvaluationSeq() != NULL ){
                                $bullEleve .= $matiere->getEvaluationSeq()->getIndex()->getEnseignant();
                            }else{
                                $bullEleve .= $matiere->getEvaluationSeq1()->getIndex()->getEnseignant();
                            }
                        }
                        $bullEleve .= '
                            </td>
                            <td style="text-align: center">';
                        if ($matiere->getEvaluationSeq() != NULL) {
                            $bullEleve .= $matiere->getEvaluationSeq()->getNote();
                        }else{
                            $bullEleve.='/';
                        }
                        $bullEleve .= '
                            </td>
                            <td style="text-align: center">';
                        if ($matiere->getEvaluationSeq1() != NULL) {
                            $bullEleve .= $matiere->getEvaluationSeq1()->getNote();
                        }else{
                            $bullEleve.='/';
                        }
                        $bullEleve .= '
                            </td>
                            <td style="text-align: center">';
                        if ($matiere->getEvaluationSeq() != NULL && $matiere->getEvaluationSeq1() != NULL) {
                            $note = ($matiere->getEvaluationSeq()->getNote() + $matiere->getEvaluationSeq1()->getNote())/2;
                            //$bullEleve .= $matiere->getEvaluationSeq()->getNote();
                            $bullEleve .= $note;
                        }else if($matiere->getEvaluationSeq() != NULL){
                            $note = $matiere->getEvaluationSeq()->getNote();
                            $bullEleve .= $note;
                        }else if($matiere->getEvaluationSeq1() != NULL){
                            $note = $matiere->getEvaluationSeq1()->getNote();
                            $bullEleve .= $note;
                        }
                        if ($matiere->getEvaluationSeqOne() != NULL && $matiere->getEvaluationSeqTwo() != NULL) {
                            $noteTrim1 = ($matiere->getEvaluationSeqOne()->getNote() + $matiere->getEvaluationSeqTwo()->getNote())/2;
                        }else if($matiere->getEvaluationSeqOne() != NULL){
                            $noteTrim1 = $matiere->getEvaluationSeqOne()->getNote();
                        }else if($matiere->getEvaluationSeqTwo() != NULL){
                            $noteTrim1 = $matiere->getEvaluationSeqTwo()->getNote();
                        }
                        if ($matiere->getEvaluationSeqThree() != NULL && $matiere->getEvaluationSeqFourth() != NULL) {
                            $noteTrim2 = ($matiere->getEvaluationSeqThree()->getNote() + $matiere->getEvaluationSeqFourth()->getNote())/2;
                        }else if($matiere->getEvaluationSeqThree() != NULL){
                            $noteTrim2 = $matiere->getEvaluationSeqThree()->getNote();
                        }else if($matiere->getEvaluationSeqFourth() != NULL){
                            $noteTrim2 = $matiere->getEvaluationSeqFourth()->getNote();
                        }
                        $bullEleve .= '
                            </td>
                            <td style="text-align: center">';
                        if($matiere->getEvaluationSeq() != NULL || $matiere->getEvaluationSeq1() != NULL) {
                            if($matiere->getEvaluationSeq() != NULL){
                                $somCoefCat = $somCoefCat + $matiere->getEvaluationSeq()->getIndex()->getCoefficient();
                                $somCoef = $somCoef + $matiere->getEvaluationSeq()->getIndex()->getCoefficient();
                                $bullEleve .= $matiere->getEvaluationSeq()->getIndex()->getCoefficient();
                            }else{
                                $somCoefCat = $somCoefCat + $matiere->getEvaluationSeq1()->getIndex()->getCoefficient();
                                $somCoef = $somCoef + $matiere->getEvaluationSeq1()->getIndex()->getCoefficient();
                                $bullEleve .= $matiere->getEvaluationSeq1()->getIndex()->getCoefficient();
                            }
                        }
                        $bullEleve .= '
                            </td>
                            <td style="text-align: center">';
                        if ($matiere->getEvaluationSeq() != NULL ||  $matiere->getEvaluationSeq1() != NULL) {
                            if($matiere->getEvaluationSeq() != NULL){
                                $total = $matiere->getEvaluationSeq()->getIndex()->getCoefficient() * $note;
                                $bullEleve .= $total;
                                $somTotalCat = $somTotalCat + $total;
                                $somNote = $somNote + $total;
                            }else{
                                $total = $matiere->getEvaluationSeq1()->getIndex()->getCoefficient() * $note;
                                $bullEleve .= $total;
                                $somTotalCat = $somTotalCat + $total;
                                $somNote = $somNote + $total;
                            }
                        }
                        if($matiere->getEvaluationSeqOne() != NULL ||  $matiere->getEvaluationSeqTwo() != NULL) {
                            if($matiere->getEvaluationSeqOne() != NULL){
                                $totalTrim1 = $matiere->getEvaluationSeqOne()->getIndex()->getCoefficient() * $noteTrim1;
                                $somNoteTrim1 = $somNoteTrim1 + $totalTrim1;
                            }else{
                                $totalTrim1 = $matiere->getEvaluationSeqTwo()->getIndex()->getCoefficient() * $noteTrim1;
                                $somNoteTrim1 = $somNoteTrim1 + $totalTrim1;
                            }
                        }
                        /*Gestion du deuxième trimestre*/
                        if($matiere->getEvaluationSeqThree() != NULL ||  $matiere->getEvaluationSeqFourth() != NULL) {
                            if($matiere->getEvaluationSeqThree() != NULL){
                                $totalTrim2 = $matiere->getEvaluationSeqThree()->getIndex()->getCoefficient() * $noteTrim2;
                                $somNoteTrim2 = $somNoteTrim2 + $totalTrim2;
                            }else{
                                $totalTrim2 = $matiere->getEvaluationSeqFourth()->getIndex()->getCoefficient() * $noteTrim2;
                                $somNoteTrim2 = $somNoteTrim2 + $totalTrim2;
                            }
                        }
                        if($matiere->getEvaluationSeqOne() != NULL || $matiere->getEvaluationSeqTwo() != NULL) {
                            if($matiere->getEvaluationSeqOne() != NULL){
                                $somCoefTrim1 = $somCoefTrim1 + $matiere->getEvaluationSeqOne()->getIndex()->getCoefficient();
                            }else{
                                $somCoefTrim1 = $somCoefTrim1 + $matiere->getEvaluationSeqTwo()->getIndex()->getCoefficient();
                            }
                        }
                        if($matiere->getEvaluationSeqThree() != NULL || $matiere->getEvaluationSeqFourth() != NULL) {
                            if($matiere->getEvaluationSeqThree() != NULL){
                                $somCoefTrim2 = $somCoefTrim2 + $matiere->getEvaluationSeqThree()->getIndex()->getCoefficient();
                            }else{
                                $somCoefTrim2 = $somCoefTrim2 + $matiere->getEvaluationSeqFourth()->getIndex()->getCoefficient();
                            }
                        }
                        $bullEleve .= '
                            </td>
                            <!--<td>
                                Rang
                            </td>-->
                            <td>';
                        if ($matiere->getEvaluationSeq() != NULL || $matiere->getEvaluationSeq1() != NULL) {
                            $bullEleve .= $this->getMention($note);
                        }
                        $bullEleve.='
                            </td>
                        </tr>';
                    }
                    $bullEleve .= '
                    <tr style="border: 2px solid black; background: wheat;">
                        <td  style="font-size: 14px; border-right: none">
                            <strong><i>' . $categorie->getNom() . '</i></strong>
                        </td>
                        <td style="border-right: none"></td>
                        <td style="border-right: none"></td>
                        <td style="border-right: none"></td>
                        <td style="border-left: none"></td>
                        <td style=" text-align:center">
                            <b>' . $somCoefCat . '</b>
                        </td>
                        <td style="text-align:center">
                            M:
                            <b>';
                    if ($somCoefCat > 0) {
                        $moyPart = number_format($somTotalCat / $somCoefCat, 2, '.', ' ');
                        $bullEleve .= number_format($somTotalCat / $somCoefCat, 2, ',', ' ');
                    } else {
                        $bullEleve .= '/';
                    }
                    $bullEleve .= '
                            </b>
                        </td>
                       <!-- <td>
                            <b>Rang</b>
                        </td>-->
                        <td style="color: blue"><strong>';
                    if ($somCoefCat != 0) {
                        $bullEleve.= $this->getMention($moyPart);
                    }
                    $bullEleve.=
                        '</strong>
                        </td>
                    </tr>';
                }
                $abs = $this->getDoctrine()->getRepository('SchoolStudentBundle:Absence')
                    ->findOneBy(
                        array(
                            'student' => $inscription,
                            'anneeScolaire' => $anneeScolaire,
                            'sequence' => $sequenceFirst
                        )
                    );
                $abs1 = $this->getDoctrine()->getRepository('SchoolStudentBundle:Absence')
                    ->findOneBy(
                        array(
                            'student' => $inscription,
                            'anneeScolaire' => $anneeScolaire,
                            'sequence' => $sequenceSecond
                        )
                    );

                if ($abs == NULL && $abs1 == NULL) {
                    $absence = 0;
                }else if($abs != NULL && $abs1 == NULL) {
                    $absence = $abs->getNbreAbsence();
                }else if($abs == NULL && $abs1 != NULL) {
                    $absence = $abs1->getNbreAbsence();
                }else{
                    $absence = $abs1->getNbreAbsence() + $abs->getNbreAbsence();
                }
                $bullEleve.='
            </table>
            <table class="conduite" style="margin-top: 15px">
                <tr style="border: 2px solid black; font-size: 11px;">
                    <td class="40p">
                        <span style="font-size: 12px; color:purple;"><u>Moy 1er Trim: </u>';
                if ($somCoefTrim1 > 0) {
                    $bullEleve.= number_format($somNoteTrim1 / $somCoefTrim1, 2, ',', ' ');
                    $moyenneTrim1 = number_format($somNoteTrim1 / $somCoefTrim1, 2, '.', ' ');
                    if($somCoefTrim1 >= ($coefTotalTrim1/2)){
                        $tabMoyenneTrim[] = $moyenneTrim1;
                    }
                } else {
                    $bullEleve.='/';
                }
                $bullEleve .='
                        </span><br>
                        <span style="font-size: 12px; color:purple"><u>Moy 2eme Trim: </u>';
                if ($somCoefTrim2 > 0) {
                    $bullEleve.= number_format($somNoteTrim2 / $somCoefTrim2, 2, ',', ' ');
                    $moyenneTrim2 = number_format($somNoteTrim2 / $somCoefTrim2, 2, '.', ' ');
                    if($somCoefTrim2 >= ($coefTotalTrim2/2)){
                        $tabMoyenneTrim[] = $moyenneTrim2;
                    }
                } else {
                    $bullEleve.='/';
                }
                    if($somCoef >= ($coefTotal/2)){
                        $moyTrim3 = number_format($somNote / $somCoef, 2, '.', ' ');
                        $tabMoyenneTrim[] = $moyTrim3;
                    }
                $bullEleve .='</span><br>
                <span style="font-size: 12px; color:purple"><u>Moy. 3eme Trim: </u>';
                $moy = '';
                if ($somCoef > 0) {
                    $bullEleve.= number_format($somNote / $somCoef, 2, ',', ' ');
                    $moy = number_format($somNote / $somCoef, 2, '.', ' ');
                    if($somCoef >= ($coefTotal/2)){
                        $tabMoy[] = $moy;
                        $tabMoyenneTrim[] = $moy;
                    }
                } else {
                    $bullEleve.='/';
                }
                $bullEleve.=' / ';
                if($somCoef >= ($coefTotal/2)){
                    $bullEleve .= 'RANG_'.$moy.'';
                }else{
                    $bullEleve .= '<span style="color: red; font-size: 0.3em;">NON CLASSE</span>';
                }
                $bullEleve .='
                </span>
                    </td>
                    <td class="20p">
                        <span style="font-size: 14px;"><u>CONDUITE</u></span><br>
                        <b><span style="font-size: 12px; text-align: center;">Abs. non justifi&eacute;es: ' . $absence . '</span></b>
                    </td>
                    <td class="20p">
                        <u>D&eacute;cision du conseil</u>:
                        ' . $this->decisionConseil($absence) . '
                    </td>
                    <td class="20p">
                        <u>Situation de l\'&eacute;l&egrave;ve:</u><<br/>
                        ' . $this->situation(($inscription)) . '
                    </td>
                </tr>
            </table>
            <table class="moyenne">
                <tr style="border: 2px solid black;">
                    <td class="20p">
                        <b style="color: blue;"><u>Moy. Gen. de la classe</u>: MOYENNE_GENERALE</b>
                    </td>
                    <td class="40p" style="color: green;">
                        <u>Moy. de l\'&eacute;l&egrave;ve</u>:<b> ';
                $moyAnnuelle = number_format(array_sum($tabMoyenneTrim) / count($tabMoyenneTrim), 2, '.', ' ');
                $bullEleve .= number_format(array_sum($tabMoyenneTrim) / count($tabMoyenneTrim), 2, ',', ' ');
                $tabMoyenneAnnuelle [] = $moyAnnuelle;
                $bullEleve.='
                        </b>
                        <br>
                        <b><u>Appr&eacute;ciation: </u> ' . $this->getMention($moyAnnuelle) . '</b><br>
                        <b style="color: orange"><u>RANG ANNUEL:</u>';
                if($somCoef >= ($coefTotal/2)){
                    $bullEleve .= 'RANGANNUELLE_'.$moyAnnuelle.'';
                }else{
                    $bullEleve .= '<span style="color: red;">NON CLASSE</span>';
                }
                $bullEleve.='</b>
                    </td>
                    <td class="40p" >
                        <b><u>OBSERVATIONS</u></b><br/>
                        <br/><br/><br/>
                    </td>
                </tr>
            </table>
            <table class="signature">
                <tr>
                    <td class="50p" style="border-bottom: none">
                        <u>Visa du parent</u><br/>
                        <br/><br/><br/>
                    </td>
                    <td class="50p" style="border-bottom: none">
                        Fait &agrave;  ' . $ecole->getVille() . ' le ' . date('d/m/Y') . '
                        <br/>
                        Le proviseur
                        <br/><br/><br/>
                    </td>
                </tr>
            </table>
            </page>';
            }

            if (count($tabMoy) != 0) {
                $moyGeneral = number_format(array_sum($tabMoy) / count($tabMoy), 2, ',', ' ');
            } else {
                $moyGeneral = '//';
            }
            $bullEleve = str_replace('MOYENNE_GENERALE', $moyGeneral, $bullEleve);
            $taille = count($tabMoy);
            sort($tabMoy);
            for ($i = $taille - 1, $j = 1; $i >= 0; $i--) {
                if ($j == 1) {
                    $bullEleve = str_replace('RANG_' . $tabMoy[$i], ($j++) . 'er(e)', $bullEleve);
                } else {
                    $bullEleve = str_replace('RANG_' . $tabMoy[$i], ($j++) . 'ème', $bullEleve);
                }
            }

            $tailleAnnuelle = count($tabMoyenneAnnuelle);
            sort($tabMoyenneAnnuelle);
            for ($i = $tailleAnnuelle - 1, $j = 1; $i >= 0; $i--) {
                if ($j == 1) {
                    $bullEleve = str_replace('RANGANNUELLE_' . $tabMoyenneAnnuelle[$i], ($j++) . 'er(e)', $bullEleve);
                } else {
                    $bullEleve = str_replace('RANGANNUELLE_' . $tabMoyenneAnnuelle[$i], ($j++) . 'ème', $bullEleve);
                }
            }

            $html = $this->renderView('SchoolNoteBundle:Bulletin:bulletinTrimClasse.html.twig', array(
                'ecole' => $ecole,
                'pays' => $pays,
                'annee' => $anneeScolaire,
                'Allstudent' => $AllstudentsteEleve,
                'titulaire' => $titulaire,
                //'sequence' => $sequence,
                'page' => $bullEleve
            ));
            $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
            $html2pdf->pdf->SetAuthor('GreenSoft-Team');
            $html2pdf->pdf->SetTitle('Bulletins' .  'Trimestriel ' . $classe->getNom());
            $html2pdf->pdf->SetSubject('Bulletin Sequentiel');
            $html2pdf->pdf->SetKeywords('Classe, Eleve, Bulletin, Notes, Trimestre');
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->writeHTML($html);
            $content = $html2pdf->Output('', true);
            $response = new Response();
            $response->setContent($content);
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-disposition', 'filename=Bulletins_'.$classe->getNom().'.pdf');
            return $response;
        } else {
            return $this->render('SchoolNoteBundle:Error:error1.html.twig');
        }
    }


    public function getMention($note) {
        if ($note <= 3)
            return 'NUL';
        if ($note < 6)
            return utf8_encode('Très Faible');
        if ($note < 8)
            return 'Faible';
        if ($note < 9)
            return 'Insuffisant';
        if ($note < 10)
            return utf8_encode('Médiocre');
        if ($note < 12)
            return 'Passable';
        if ($note < 14)
            return 'Assez-Bien';
        if ($note < 16)
            return 'Bien';
        if ($note < 18)
            return utf8_encode('Très Bien');
        if ($note < 20)
            return 'Excellent';
        if ($note == 20)
            return 'Parfait';
    }
    public function getRang($note, $tabNote) {
        // $i;
        $size = count($tabNote);
        for ($i = 0; i < $size; $i++) {
            if ($tabNote[$i] == $note)
                return $i + 1;
        }
    }
    public function decisionConseil($absence) {
        $result = '<b>';
        if ($absence == 0) {
            $result.= 'RAS';
        }
        if ($absence >= 1 && $absence <= 5 ) {
            $result.= utf8_encode('Attention à la discipline');
        }
        if ($absence >= 6 && $absence <= 12 ) {
            $result.= utf8_encode('Avertissement en conduite');
        }
        if ($absence >= 13 && $absence <= 18 ) {
            $result.= utf8_encode('Blâme conduite');
        }
        if ($absence >= 19) {
            $result.= utf8_encode('Blâme conduite');
        }
        $result.='</b>';
        return $result;
    }
    public function situation($eleve) {
        return ($eleve->getStatus() == 0) ? '<b style="color: red">Insolvable</b>' : '<b style="color: gray">Solvable</b>';
    }
}
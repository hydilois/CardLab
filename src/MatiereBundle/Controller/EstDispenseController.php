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

        if ($form->isSubmitted()) {

            $em = $this->getDoctrine()->getManager();
            $school = $em->getRepository('ConfigBundle:Constante')->findAll();
            $anneEncour = $em->getRepository('ConfigBundle:Annee')->findOneBy(['isAnneeEnCour' => true]);
            $testEnseignementExist = $this->getDoctrine()->getRepository('MatiereBundle:EstDispense')->findBy(
                array(
                    'enseignant' => $estDispense->getEnseignant(),
                    'matiere' => $estDispense->getMatiere(),
                    'classe' => $estDispense->getClasse(),
                    'annee' => $anneEncour,
                ));
            if($testEnseignementExist){
                //die("Enseignement existe");
                $request->getSession()->getFlashBag()->add('notice', 'Cet Enseignement est déjà enregistré.');
            }else if($estDispense->getTitulaire()){
                $enseignantTitulaire = $this->getDoctrine()->getRepository('MatiereBundle:EstDispense')->findBy(
                    array(
                        'classe' => $estDispense->getClasse(),
                        'annee' => $anneEncour,
                        'titulaire' => true,
                    ));
                if($enseignantTitulaire){
                    //die("Titulaire existant");
                    $request->getSession()->getFlashBag()->add('notice', 'la classe a déjà un titulaire.');
                }else {
                    $estDispense->setAnnee($anneEncour);
                    $em->persist($estDispense);
                    $em->flush();

                    $estDispenses = $em->getRepository('MatiereBundle:EstDispense')->findAll();

                    return $this->redirectToRoute('estdispense_index', array(
                        'estDispenses' => $estDispenses,
                        ));
                }
            }else{
                $estDispense->setAnnee($anneEncour);
                $em->persist($estDispense);
                $em->flush();

                $estDispenses = $em->getRepository('MatiereBundle:EstDispense')->findAll();

                return $this->redirectToRoute('estdispense_index', array(
                    'estDispenses' => $estDispenses,
                    ));
            }
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

        if ($editForm->isSubmitted()) {
            $this->getDoctrine()->getManager()->flush();

            $em = $this->getDoctrine()->getManager();

            $estDispenses = $em->getRepository('MatiereBundle:EstDispense')->findAll();

            return $this->redirectToRoute('estdispense_index', array(
                'estDispenses' => $estDispenses,
            ));
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

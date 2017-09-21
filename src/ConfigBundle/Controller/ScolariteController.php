<?php

namespace ConfigBundle\Controller;

use ConfigBundle\Entity\Scolarite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Scolarite controller.
 *
 * @Route("scolarite")
 */
class ScolariteController extends Controller
{
    /**
     * Lists all scolarite entities.
     *
     * @Route("/", name="scolarite_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $scolarites = $em->getRepository('ConfigBundle:Scolarite')->findAll();

        $scolarite = new Scolarite();
        $formScolarite = $this->createForm('ConfigBundle\Form\ScolariteType', $scolarite);
        $formScolarite->handleRequest($request);

        return $this->render('scolarite/index.html.twig', array(
            'scolarites' => $scolarites,
            'formScolarite' => $formScolarite->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing scolarite entity.
     *
     * @Route("/{id}/edit", name="scolarite_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Scolarite $scolarite)
    {
        //$deleteForm = $this->createDeleteForm($scolarite);
        $editForm = $this->createForm('ConfigBundle\Form\ScolariteType', $scolarite);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('scolarite_edit', array('id' => $scolarite->getId()));
        }

        return $this->render('scolarite/edit.html.twig', array(
            'scolarite' => $scolarite,
            'edit_form' => $editForm->createView(),
          //  'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a scolarite entity.
     *
     * @Route("/{id}", name="scolarite_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Scolarite $scolarite)
    {
        $form = $this->createDeleteForm($scolarite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($scolarite);
            $em->flush();
        }

        return $this->redirectToRoute('scolarite_index');
    }

    /**
     * Creates a form to delete a scolarite entity.
     *
     * @param Scolarite $scolarite The scolarite entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Scolarite $scolarite)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('scolarite_delete', array('id' => $scolarite->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @param Request $request [contains the http request that is passed on]
     * 
     * @Route("/new_json", name="scolarite_new_json")
     * @Method({"GET", "POST"})
     */
    function addNewScolariteFromJSON(Request $request){

        $entityManager = $this->getDoctrine()->getManager();

        $logger = $this->get('logger');

        $scolarite = new Scolarite();
        try {
            //first thing we get the mouvement with the JSON format
            $scolariteJSON = json_decode(json_encode($request->request->get('data')), true);

            $anneeEnCour = $entityManager->getRepository('ConfigBundle:Annee')->find($scolariteJSON["anneeEnCour"]);
            //Tester si la scolarité ayant les même caractéristiques est déja enregistrée
            $scolariteTemp = $entityManager->getRepository('ConfigBundle:Scolarite')->findOneBy(
                [
                    'cycle' => $scolariteJSON['cycle'],
                    'anneeEnCour' => $anneeEnCour,
                ]);
            if ($scolariteTemp) {//l'instance de la scolarite existe
                $response["data"]               = $scolariteJSON;
                 //we say everything went well
                 $response["status"] = false;

                 return new Response(json_encode($response));
            }
            $scolarite->setMontantContributionExigible($scolariteJSON["montantContributionExigible"]);
            $scolarite->setMontantApee($scolariteJSON["montantApee"]);
            $scolarite->setAnneeEnCour($anneeEnCour);
            $scolarite->setCycle($scolariteJSON["cycle"]);

        /**
         * Enregistrement ici
         * --------------------
         */
        
        $entityManager->persist($scolarite);
        $entityManager->flush();

        $response["data"]               = $scolariteJSON;
        $response['message'] = "on ne peut pas avoir deux frais de scolarite pour un même cycle à la même année";

        //we say everything went well
        $response["status"] = true;
       
        } catch (Exception $ex) {
            $logger("Une erreur est survenue");
            $response["status"] = false;
        }

        return new Response(json_encode($response));
    }

     /**
      * this function allow a person to get a Classe in a JSON Format
      * the main use of this function is for assync calls
      * @param  Request $request [description]
      * @return JSON  
      * a complexe object indicating status and the information requested
      *
      * @Route("/getScolarite")
      * @Method("POST")
      */
    public function getClasse(Request $request){
        $requestParsed = json_decode(json_encode($request->request->get('data')));
        $idScolarite    = $requestParsed->idScolarite;

        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQueryBuilder()
                ->select('s')
                ->from('ConfigBundle:Scolarite', 's')
                ->where('s.id = ' . $idScolarite)
                ->getQuery();

        $scolariteArray = $query->getSingleResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        $response = [
                "message" => "Entite Scolarite", 
                "params" => $idScolarite, 
                "status" => "success", 
                "data" => json_decode(json_encode($scolariteArray))
            ];
        return new Response(json_encode($response));
    }
}

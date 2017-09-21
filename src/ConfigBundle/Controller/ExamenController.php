<?php

namespace ConfigBundle\Controller;

use ConfigBundle\Entity\Examen;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * examen controller.
 *
 * @Route("examen")
 */
class ExamenController extends Controller
{
    /**
     * Lists all examen entities.
     *
     * @Route("/", name="examen_index")
     * @Method("GET")
     */
    public function indexAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $examens = $em->getRepository('ConfigBundle:Examen')->findAll();
        $examen = new Examen();
        $formExamen = $this->createForm('ConfigBundle\Form\ExamenType', $examen);
        $formExamen->handleRequest($request); 

        return $this->render('examen/index.html.twig', array(
            'examens' => $examens,
            'formExamen' => $formExamen->createView(),
        ));
    }

    /**
     * Creates a new examen entity.
     *
     * @Route("/new", name="examen_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $examen = new Examen();
        $form = $this->createForm('ConfigBundle\Form\ExamenType', $examen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($examen);
            $em->flush();

            return $this->redirectToRoute('examen_show', array('id' => $examen->getId()));
        }

        return $this->render('examen/new.html.twig', array(
            'examen' => $examen,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a examen entity.
     *
     * @Route("/{id}", name="examen_show")
     * @Method("GET")
     */
    public function showAction(Examen $examen)
    {
        $deleteForm = $this->createDeleteForm($examen);

        return $this->render('examen/show.html.twig', array(
            'examen' => $examen,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing examen entity.
     *
     * @Route("/{id}/edit", name="examen_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Examen $examen)
    {
        $deleteForm = $this->createDeleteForm($examen);
        $editForm = $this->createForm('ConfigBundle\Form\ExamenType', $examen);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('examen_index');
        }

        return $this->render('examen/edit.html.twig', array(
            'examen' => $examen,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a examen entity.
     *
     * @Route("/{id}", name="examen_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Examen $examen)
    {
        $form = $this->createDeleteForm($examen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($examen);
            $em->flush();
        }

        return $this->redirectToRoute('examen_index');
    }

    /**
     * Creates a form to delete a examen entity.
     *
     * @param Examen $examen The examen entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Examen $examen)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('examen_delete', array('id' => $examen->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @param Request $request [contains the http request that is passed on]
     * 
     * @Route("/new_json", name="examen_new_json")
     * @Method({"GET", "POST"})
     */
    function addNewExamenFromJSON(Request $request){

        $entityManager = $this->getDoctrine()->getManager();

        $logger = $this->get('logger');

        $examen = new Examen();
        try {
            //first thing we get the mouvement with the JSON format
            $examenJSON = json_decode(json_encode($request->request->get('data')), true);

            $anneeEnCour = $entityManager->getRepository('ConfigBundle:Annee')->find($examenJSON["anneeEnCour"]);
            $classe = $entityManager->getRepository('IntendanceBundle:Classe')->find($examenJSON['classe']);
            //Tester si l'examen ayant les mêmes caractéristiques est déja enregistrée
            $examenTemp = $entityManager->getRepository('ConfigBundle:Examen')->findOneBy(
                [
                    'nom' => $examenJSON['nom'],
                    'anneeEnCour' => $anneeEnCour,
                    'classe' => $classe,
                ]);
            if ($examenTemp) {//l'instance de l'examen existe
                $response["data"]               = $examenJSON;
                 //we say everything didnit work well
                 $response["status"] = false;
                 $response['message'] = "un frais d'examen pour une même année, une même classe ne peut être enregistré. pas de duplication des informations.";

                 return new Response(json_encode($response));
            }
            $examen->setNom($examenJSON["nom"]);
            $examen->setMontant($examenJSON["montant"]);
            $examen->setAnneeEnCour($anneeEnCour);
            $examen->setClasse($classe);

        /**
         * Enregistrement ici
         * --------------------
         */
        
        $entityManager->persist($examen);
        $entityManager->flush();

        $response["data"]               = $examenJSON;
        $response['message'] = "le frais d'examen  a été enregistré avec succès";

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
      * @Route("/getExamen")
      * @Method("POST")
      */
    public function getClasse(Request $request){
        $requestParsed = json_decode(json_encode($request->request->get('data')));
        $idExamen    = $requestParsed->idExamen;

        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQueryBuilder()
                ->select('e')
                ->from('ConfigBundle:Examen', 'e')
                ->where('e.id = ' . $idExamen)
                ->getQuery();

        $examenArray = $query->getSingleResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        $response = [
                "message" => "Entite Examen", 
                "params" => $idExamen, 
                "status" => "success", 
                "data" => json_decode(json_encode($examenArray))
            ];
        return new Response(json_encode($response));
    }
}

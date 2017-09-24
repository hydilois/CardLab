<?php

namespace ConfigBundle\Controller;

use ConfigBundle\Entity\Constante;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Constante controller.
 *
 * @Route("constante")
 */

class ConstanteController extends Controller {

    /**
     * Lists all constante entities.
     *
     * @Route("/", name="constante_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $constantes = $em->getRepository('ConfigBundle:Constante')->findAll();

        return $this->render('constante/index.html.twig', array(
                    'constantes' => $constantes,
        ));
    }

    /**
     * Creates a new constante entity.
     *
     * @Route("/new", name="constante_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $constante = new Constante();
        $form = $this->createForm('ConfigBundle\Form\ConstanteType', $constante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fichier = '';

            $fichier = $this->importation($fichier);

            $constante->setLogo($fichier);

            $em = $this->getDoctrine()->getManager();
            $em->persist($constante);
            $em->flush();

            return $this->redirectToRoute('constante_show', array('id' => $constante->getId()));
        }

        return $this->render('constante/new.html.twig', array(
                    'constante' => $constante,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a constante entity.
     *
     * @Route("/{id}", name="constante_show")
     * @Method("GET")
     */
    public function showAction(Constante $constante) {
        $deleteForm = $this->createDeleteForm($constante);

        return $this->render('constante/show.html.twig', array(
                    'constante' => $constante,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing constante entity.
     *
     * @Route("/{id}/edit", name="constante_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Constante $constante) {
        $deleteForm = $this->createDeleteForm($constante);

        $file = '';
        $ancien = '';
        if ($constante->getLogo() != "") {
            $ancien = $constante->getLogo();
            $file = new \Symfony\Component\HttpFoundation\File\File(('Uploads/Logo/' . $constante->getLogo()));
        }

        $constante->setLogo($file);
        $editForm = $this->createForm('ConfigBundle\Form\ConstanteType', $constante);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $dossier = $this->get('kernel')->getRootDir() . '/../web/Uploads/Logo/';

            if ($ancien != '') {
                chmod($dossier . $ancien, 777);
                unlink($dossier . $ancien);
            }

            $fichier = '';
            $fichier = $this->importation($fichier);

            $constante->setLogo($fichier);
            $em = $this->getDoctrine()->getManager();
            $em->persist($constante);
            $em->flush();

            return $this->redirectToRoute('constante_edit', array('id' => $constante->getId()));
        }

        return $this->render('constante/edit.html.twig', array(
                    'constante' => $constante,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'file' => $file,
        ));
    }

    public function importation($fichier) {
        $dossier = $this->get('kernel')->getRootDir() . '/../web/Uploads/Logo/';
        // importation du fichier
        $fichier = basename($_FILES['avatar']['name']);
        $taille_maxi = 4000000;
        $taille = filesize($_FILES['avatar']['tmp_name']);
        $extensions = array('.gif', '.jpg', '.jpeg');
        $extension = strrchr($_FILES['avatar']['name'], '.');
        //Début des vérifications de sécurité...
        if (!in_array($extension, $extensions)) { //Si l'extension n'est pas dans le tableau
            $erreur = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg, txt ou doc...';
        }
        if ($taille > $taille_maxi) {
            $erreur = 'Le fichier est trop gros...';
        }
        if (!isset($erreur)) { //S'il n'y a pas d'erreur, on upload
            //On formate le nom du fichier ici...
            $fichier = strtr($fichier, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
            $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dossier . $fichier)) { //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
                echo 'Upload effectué avec succès !';
            } else { //Sinon (la fonction renvoie FALSE).
                echo 'Echec de l\'upload !';
            }
        } else {
            echo $erreur;
        }
        return $fichier;
    }

    /**
     * Deletes a constante entity.
     *
     * @Route("/{id}", name="constante_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Constante $constante) {
        $form = $this->createDeleteForm($constante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dossier = $this->get('kernel')->getRootDir() . '/../web/Uploads/Logo/';
            chmod($dossier . $constante->getLogo(), 777);
            unlink($dossier . $constante->getLogo());

            $em = $this->getDoctrine()->getManager();
            $em->remove($constante);
            $em->flush();
        }

        return $this->redirectToRoute('constante_index');
    }

    /**
     * Creates a form to delete a constante entity.
     *
     * @param Constante $constante The constante entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Constante $constante) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('constante_delete', array('id' => $constante->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}

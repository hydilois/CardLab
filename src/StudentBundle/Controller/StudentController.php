<?php

namespace StudentBundle\Controller;

use StudentBundle\Entity\Student;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Student controller.
 *
 * @Route("student")
 */
class StudentController extends Controller {

    /**
     * Lists all student entities.
     *
     * @Route("/", name="student_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $students = $em->getRepository('StudentBundle:Student')->findAll();

        return $this->render('student/index.html.twig', array(
                    'students' => $students,
        ));
    }

    /**
     * Creates a new student entity.
     *
     * @Route("/new", name="student_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $student = new Student();
        $form = $this->createForm('StudentBundle\Form\StudentType', $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fichier = '';
            $fichier = $this->importation($fichier);
            $student->setPhoto($fichier);

            $em = $this->getDoctrine()->getManager();
            $em->persist($student);
            $em->flush();

            return $this->redirectToRoute('student_show', array('id' => $student->getId()));
        }

        return $this->render('student/new.html.twig', array(
                    'student' => $student,
                    'form' => $form->createView(),
        ));
    }

    public function importation($fichier) {
        $dossier = $this->get('kernel')->getRootDir() . '/../web/Uploads/Photos/';
        // importation du fichier
        $fichier = basename($_FILES['avatar']['name']);
        $taille_maxi = 4000000;
        $taille = filesize($_FILES['avatar']['tmp_name']);
        $extensions = array('.png', '.gif', '.jpg', '.jpeg');
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
     * Finds and displays a student entity.
     *
     * @Route("/{id}", name="student_show")
     * @Method("GET")
     */
    public function showAction(Student $student) {
        $deleteForm = $this->createDeleteForm($student);

        return $this->render('student/show.html.twig', array(
                    'student' => $student,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing student entity.
     *
     * @Route("/{id}/edit", name="student_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Student $student) {
        $deleteForm = $this->createDeleteForm($student);

        $file = '';
        $ancien = '';
        if ($student->getPhoto() != "") {
            $ancien = $student->getPhoto();
            $file = new \Symfony\Component\HttpFoundation\File\File(('Uploads/Photos/' . $student->getPhoto()));
        }
        $student->setPhoto($file);

        $editForm = $this->createForm('StudentBundle\Form\StudentType', $student);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $dossier = $this->get('kernel')->getRootDir() . '/../web/Uploads/Photos/';

            if ($ancien != '') {
                chmod($dossier . $ancien, 777);
                unlink($dossier . $ancien);
            }

            $fichier = '';
            $fichier = $this->importation($fichier);

            $student->setPhoto($fichier);
            $em = $this->getDoctrine()->getManager();
            $em->persist($student);
            $em->flush();

            return $this->redirectToRoute('student_edit', array('id' => $student->getId()));
        }

        return $this->render('student/edit.html.twig', array(
                    'student' => $student,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a student entity.
     *
     * @Route("/{id}", name="student_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Student $student) {
        $form = $this->createDeleteForm($student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dossier = $this->get('kernel')->getRootDir() . '/../web/Uploads/Photos/';
            chmod($dossier . $student->getPhoto(), 777);
            unlink($dossier . $student->getPhoto());

            $em = $this->getDoctrine()->getManager();
            $em->remove($student);
            $em->flush();
        }

        return $this->redirectToRoute('student_index');
    }

    /**
     * Creates a form to delete a student entity.
     *
     * @param Student $student The student entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Student $student) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('student_delete', array('id' => $student->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}

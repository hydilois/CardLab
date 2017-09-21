<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request){
        
        // Test is the user does not have the default role
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->container->get ('router')->generate ('fos_user_security_login'));
        }

        // $entityManager = $this->getDoctrine()->getManager();

        // //fetch all students
        // $elevesEnregistres = $entityManager->getRepository('IntendanceBundle:Eleve')->findAll();
        
        // // BACALAUREAT
        // $classeTerminale = $entityManager->getRepository('IntendanceBundle:Classe')->find(1);
        
        // $classesFillesTerminale = $entityManager->getRepository('IntendanceBundle:Classe')->findByClasseMere($classeTerminale);

        //     //intialiser le compteur pour les élèves de la terminale
        // $countEleveTerminale = 0;
        // $counterPaiementTerminale = 0;
        // foreach ($classesFillesTerminale as $key => $value) {

        //     $qbEleveTerminale = $entityManager->createQueryBuilder();
        //     $qbPaiementTerminale = $entityManager->createQueryBuilder();
        //     //fetch all student doing baccalaureat by classes
        //     $qbEleveTerminale->select('e')
        //         ->from('IntendanceBundle:Eleve', 'e')
        //         ->innerJoin('IntendanceBundle:Classe', 'c','WITH' ,'c.id = e.classe')
        //         ->andWhere('c.id= :identifiant')
        //         ->setParameters(
        //             [
        //             'identifiant' => $value->getId(),
        //             ]);

        //     $elevesBAC = $qbEleveTerminale->getQuery()->getResult();
        //     $countEleveTerminale = $countEleveTerminale + count($elevesBAC);

        //     $qbPaiementTerminale->select('p')
        //         ->from('IntendanceBundle:Paiement', 'p')
        //         ->innerJoin('IntendanceBundle:Eleve', 'e','WITH' ,'e.id = p.eleve')
        //         ->innerJoin('IntendanceBundle:Classe', 'c','WITH' ,'c.id = e.classe')
        //         ->innerJoin('ConfigBundle:Annee', 'a','WITH' ,'a.id = p.annee')
        //         ->where('a.isAnneeEnCour = true')
        //         ->andWhere('p.examen IS NOT NULL')
        //         ->andWhere('e.classe ='.$value->getId())
        //         ;
        //     $paiementTerminale = $qbPaiementTerminale->getQuery()->getResult();
        //     $counterPaiementTerminale = $counterPaiementTerminale  + count($paiementTerminale);
        // }

        //         // Premiere
        // $classePremiere = $entityManager->getRepository('IntendanceBundle:Classe')->find(30);
        
        // $classesFillesPremiere = $entityManager->getRepository('IntendanceBundle:Classe')->findByClasseMere($classePremiere);

        //     //intialiser le compteur pour les élèves de la première
        // $countElevePremiere = 0;
        // $counterPaiementPremiere = 0;
        // foreach ($classesFillesPremiere as $key => $value) {

        //     $qbElevePremiere = $entityManager->createQueryBuilder();
        //     $qbPaiementPremiere = $entityManager->createQueryBuilder();
        //     //fetch all student doing probatoire by classes
        //     $qbElevePremiere->select('e')
        //         ->from('IntendanceBundle:Eleve', 'e')
        //         ->innerJoin('IntendanceBundle:Classe', 'c','WITH' ,'c.id = e.classe')
        //         ->andWhere('c.id= :identifiant')
        //         ->setParameters(
        //             [
        //             'identifiant' => $value->getId(),
        //             ]);

        //     $elevesPremiere = $qbElevePremiere->getQuery()->getResult();
        //     $countElevePremiere = $countElevePremiere + count($elevesPremiere);

        //     $qbPaiementPremiere->select('p')
        //         ->from('IntendanceBundle:Paiement', 'p')
        //         ->innerJoin('IntendanceBundle:Eleve', 'e','WITH' ,'e.id = p.eleve')
        //         ->innerJoin('IntendanceBundle:Classe', 'c','WITH' ,'c.id = e.classe')
        //         ->innerJoin('ConfigBundle:Annee', 'a','WITH' ,'a.id = p.annee')
        //         ->where('a.isAnneeEnCour = true')
        //         ->andWhere('p.examen IS NOT NULL')
        //         ->andWhere('e.classe ='.$value->getId())
        //         ;
        //     $paiementPremiere = $qbPaiementPremiere->getQuery()->getResult();
        //     $counterPaiementPremiere = $counterPaiementPremiere  + count($paiementPremiere);
        // }

        // // Troisième
        // $classeTroisieme = $entityManager->getRepository('IntendanceBundle:Classe')->find(3);
        
        // $classesFillesTroisieme = $entityManager->getRepository('IntendanceBundle:Classe')->findByClasseMere($classeTroisieme);

        //     //intialiser le compteur pour les élèves de la terminale
        // $countEleveTroisieme = 0;
        // $counterPaiementTroisieme = 0;
        // foreach ($classesFillesTroisieme as $key => $value) {

        //     $qbEleveTroisieme = $entityManager->createQueryBuilder();
        //     $qbPaiementTroisieme = $entityManager->createQueryBuilder();
        //     //fetch all student doing BEPC by classes
        //     $qbEleveTroisieme->select('e')
        //         ->from('IntendanceBundle:Eleve', 'e')
        //         ->innerJoin('IntendanceBundle:Classe', 'c','WITH' ,'c.id = e.classe')
        //         ->andWhere('c.id= :identifiant')
        //         ->setParameters(
        //             [
        //             'identifiant' => $value->getId(),
        //             ]);

        //     $elevesBEPC = $qbEleveTroisieme->getQuery()->getResult();
        //     $countEleveTroisieme = $countEleveTroisieme + count($elevesBEPC);

        //     $qbPaiementTroisieme->select('p')
        //         ->from('IntendanceBundle:Paiement', 'p')
        //         ->innerJoin('IntendanceBundle:Eleve', 'e','WITH' ,'e.id = p.eleve')
        //         ->innerJoin('IntendanceBundle:Classe', 'c','WITH' ,'c.id = e.classe')
        //         ->innerJoin('ConfigBundle:Annee', 'a','WITH' ,'a.id = p.annee')
        //         ->where('a.isAnneeEnCour = true')
        //         ->andWhere('p.examen IS NOT NULL')
        //         ->andWhere('e.classe ='.$value->getId())
        //         ;
        //     $paiementTroisieme = $qbPaiementTroisieme->getQuery()->getResult();
        //     $counterPaiementTroisieme = $counterPaiementTroisieme  + count($paiementTroisieme);
        // }

        // $qbPaiement = $entityManager->createQueryBuilder();
        // $qbPremier = $entityManager->createQueryBuilder();
        // $qbSecond = $entityManager->createQueryBuilder();



        // // fetch all the payment of the current year
        // $qbPaiement->select('p')
        //     ->from('IntendanceBundle:Paiement', 'p')
        //     ->innerJoin('ConfigBundle:Annee', 'a','WITH' ,'a.id = p.annee')
        //     ->where('a.isAnneeEnCour = true  ')
        //     ;
        // $paiements = $qbPaiement->getQuery()->getResult();

        // //fetch the students of the first Cycle
        // $qbPremier->select('e')
        //     ->from('IntendanceBundle:Eleve', 'e')
        //     ->innerJoin('IntendanceBundle:Classe', 'c','WITH' ,'c.id = e.classe')
        //     ->where('c.cycle= :cycleClasse')
        //     ->setParameters(
        //         [
        //         'cycleClasse' => true,
        //         ]);

        // $elevesPremierCycle = $qbPremier->getQuery()->getResult();

        // //fecth the students of the second cycle
        // $qbSecond->select('e')
        //     ->from('IntendanceBundle:Eleve', 'e')
        //     ->innerJoin('IntendanceBundle:Classe', 'c','WITH' ,'c.id = e.classe')
        //     ->where('c.cycle= :cycleClasse')
        //     ->setParameters(
        //         [
        //         'cycleClasse' => false,
        //         ]);

        // $elevesSecondCycle = $qbSecond->getQuery()->getResult();


        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            // 'nombrePaiement' => count($paiements),
            // 'nombreElevesEnregistres' => count($elevesEnregistres),
            // 'elevesPremierCycle' => count($elevesPremierCycle),
            // 'elevesSecondCycle' => count($elevesSecondCycle),
            // 'statBac' => ($counterPaiementTerminale * 100)/$countEleveTerminale,
            // 'statProb' => ($counterPaiementPremiere * 100)/$countElevePremiere,
            // 'statBEPC' => ($counterPaiementTroisieme * 100)/$countEleveTroisieme,
        ]);
    }
}

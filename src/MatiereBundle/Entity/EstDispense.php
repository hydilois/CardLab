<?php

namespace MatiereBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstDispense
 *
 * @ORM\Table(name="est_dispense")
 * @ORM\Entity(repositoryClass="MatiereBundle\Repository\EstDispenseRepository")
 */
class EstDispense
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="MatiereBundle\Entity\Matiere")
     * @Assert\NotBlank()
     */
    private $matiere;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Utilisateur")
     * @Assert\NotBlank()
     */
    private $enseignant;

    /**
     * @ORM\ManyToOne(targetEntity="StudentBundle\Entity\Classe")
     * @Assert\NotBlank()
     */
    private $classe;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $coefficient;

    /**
     * @ORM\ManyToOne(targetEntity="ConfigBundle\Entity\Annee")
     * @Assert\NotBlank()
     */
    private $annee;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $nombreHeuresAnnuel;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $nombreLeconsAnnuel;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $titulaire;

    private $nbreFilles;
    private $nbreGarcons;

    private $nbreHeures;
    private $nbreLecons;

    private $compteurGarcons;
    private $compteurFilles;

    private $nbreEvaluations;

    private $moyenneGenerale;

    private $listeNotes;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set coefficient
     *
     * @param integer $coefficient
     * @return EstDispense
     */
    public function setCoefficient($coefficient) {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * Get coefficient
     *
     * @return integer 
     */
    public function getCoefficient() {
        return $this->coefficient;
    }


    /**
     * Set nombreHeuresAnnuel
     *
     * @param integer $nombreHeuresAnnuel
     * @return EstDispense
     */
    public function setNombreHeuresAnnuel($nombreHeuresAnnuel) {
        $this->nombreHeuresAnnuel = $nombreHeuresAnnuel;

        return $this;
    }

    /**
     * Get nombreHeuresAnnuel
     *
     * @return integer 
     */
    public function getNombreHeuresAnnuel() {
        return $this->nombreHeuresAnnuel;
    }

    /**
     * Set matiere
     *
     * @param \MatiereBundle\Entity\Matiere $matiere
     * @return EstDispense
     */
    public function setMatiere(\MatiereBundle\Entity\Matiere $matiere = null) {
        $this->matiere = $matiere;

        return $this;
    }

    /**
     * Get matiere
     *
     * @return \MatiereBundle\Entity\Matiere 
     */
    public function getMatiere() {
        return $this->matiere;
    }

    /**
     * Set classe
     *
     * @param \StudentBundle\Entity\Classe $classe
     * @return EstDispense
     */
    public function setClasse(\StudentBundle\Entity\Classe $classe = null) {
        $this->classe = $classe;

        return $this;
    }

    /**
     * Get classe
     *
     * @return \StudentBundle\Entity\Classe 
     */
    public function getClasse() {
        return $this->classe;
    }

    /**
     * Set enseignant
     *
     * @param \UserBundle\Entity\Utilisateur $enseignant
     * @return EstDispense
     */
    public function setEnseignant(\UserBundle\Entity\Utilisateur $enseignant = null) {
        $this->enseignant = $enseignant;

        return $this;
    }

    /**
     * Get enseignant
     *
     * @return \UserBundle\Entity\Utilisateur 
     */
    public function getEnseignant() {
        return $this->enseignant;
    }

    /**
     * Set titulaire
     *
     * @param boolean $titulaire
     * @return EstDispense
     */
    public function setTitulaire($titulaire) {
        $this->titulaire = $titulaire;

        return $this;
    }

    /**
     * Get titulaire
     *
     * @return boolean 
     */
    public function getTitulaire() {
        return $this->titulaire;
    }


    /**
     * Set annee
     *
     * @param \ConfigBundle\Entity\Annee $annee
     * @return EstDispense
     */
    public function setAnnee(\ConfigBundle\Entity\Annee $annee = null)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return \ConfigBundle\Entity\Annee 
     */
    public function getAnnee()
    {
        return $this->annee;
    }


    public function setNbreFilles($nbreFilles){
        $this->nbreFilles= $nbreFilles;
        return $this;
    }


    public function getNbreFilles(){
        return $this->nbreFilles;
    }

    public function setNbreGarcons($nbreGarcons){
        $this->nbreGarcons= $nbreGarcons;
        return $this;
    }


    public function getNbreGarcons(){
        return $this->nbreGarcons;
    }

    public function setNbreHeures($nbreHeures){
        $this->nbreHeures= $nbreHeures;
        return $this;
    }
    public function getNbreHeures(){
        return $this->nbreHeures;
    }


    public function setNbreLecons($nbreLecons){
        $this->nbreLecons= $nbreLecons;
        return $this;
    }

    public function getNbreLecons(){
        return $this->nbreLecons;
    }

    public function setCompteurGarcons($compteurGarcons){
        $this->compteurGarcons= $compteurGarcons;
        return $this;
    }

    public function getCompteurGarcons(){
        return $this->compteurGarcons;
    }
    public function setCompteurFilles($compteurFilles){
        $this->compteurFilles= $compteurFilles;
        return $this;
    }

    public function getCompteurFilles(){
        return $this->compteurFilles;
    }

    /**
     * @return mixed
     */
    public function getNbreEvaluations()
    {
        return $this->nbreEvaluations;
    }

    /**
     * @param mixed $nbreEvaluations
     */
    public function setNbreEvaluations($nbreEvaluations)
    {
        $this->nbreEvaluations = $nbreEvaluations;
    }



    /**
     * Set nombreLeconsAnnuel
     *
     * @param integer $nombreLeconsAnnuel
     * @return EstDispense
     */
    public function setNombreLeconsAnnuel($nombreLeconsAnnuel)
    {
        $this->nombreLeconsAnnuel = $nombreLeconsAnnuel;

        return $this;
    }

    /**
     * Get nombreLeconsAnnuel
     *
     * @return integer 
     */
    public function getNombreLeconsAnnuel()
    {
        return $this->nombreLeconsAnnuel;
    }

    /**
     * @return mixed
     */
    public function getMoyenneGenerale()
    {
        return $this->moyenneGenerale;
    }

    /**
     * @param mixed $moyenneGenerale
     */
    public function setMoyenneGenerale($moyenneGenerale)
    {
        $this->moyenneGenerale = $moyenneGenerale;
    }

    /**
     * @return mixed
     */
    public function getListeNotes()
    {
        return $this->listeNotes;
    }

    /**
     * @param mixed $listeNotes
     */
    public function setListeNotes($listeNotes)
    {
        $this->listeNotes = $listeNotes;
    }
}


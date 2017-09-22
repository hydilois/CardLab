<?php

namespace MatiereBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Matiere
 *
 * @ORM\Table(name="matiere")
 * @ORM\Entity(repositoryClass="MatiereBundle\Repository\MatiereRepository")
 */
class Matiere
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
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nom;
    private $taille;

    public function __toString() {
        return $this->getNom();
    }

    function getTaille() {
        return $this->taille;
    }

    function setTaille($taille) {
        $this->taille = $taille;
    }

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $abreviation;

    /**
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @Assert\NotBlank()
     */
    private $categorie;
    private $evaluationSeq;
    private $evaluationSeq1;
    private $evaluationSeqOne;
    private $evaluationSeqTwo;
    private $evaluationSeqThree;
    private $evaluationSeqFourth;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Matiere
     */
    public function setNom($nom) {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom() {
        return $this->nom;
    }

    /**
     * Set coefficient
     *
     * @param integer $coefficient
     * @return Matiere
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
     * Set abreviation
     *
     * @param string $abreviation
     * @return Matiere
     */
    public function setAbreviation($abreviation) {
        $this->abreviation = $abreviation;

        return $this;
    }

    /**
     * Get abreviation
     *
     * @return string 
     */
    public function getAbreviation() {
        return $this->abreviation;
    }

    /**
     * Set categorie
     *
     * @param \MatiereBundle\Entity\Categorie $categorie
     * @return Matiere
     */
    public function setCategorie(\MatiereBundle\Entity\Categorie $categorie = null) {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \MatiereBundle\Entity\Categorie 
     */
    public function getCategorie() {
        return $this->categorie;
    }

    function getEvaluationSeq() {
        return $this->evaluationSeq;
    }

    function setEvaluationSeq($evaluationSeq) {
        $this->evaluationSeq = $evaluationSeq;
    }

    /**
     * @return mixed
     */
    public function getEvaluationSeq1()
    {
        return $this->evaluationSeq1;
    }

    /**
     * @param mixed $evaluationSeq1
     */
    public function setEvaluationSeq1($evaluationSeq1)
    {
        $this->evaluationSeq1 = $evaluationSeq1;
    }

    /**
     * @return mixed
     */
    public function getEvaluationSeqOne()
    {
        return $this->evaluationSeqOne;
    }

    /**
     * @param mixed $evaluationSeqOne
     */
    public function setEvaluationSeqOne($evaluationSeqOne)
    {
        $this->evaluationSeqOne = $evaluationSeqOne;
    }

    /**
     * @return mixed
     */
    public function getEvaluationSeqTwo()
    {
        return $this->evaluationSeqTwo;
    }

    /**
     * @param mixed $evaluationSeqTwo
     */
    public function setEvaluationSeqTwo($evaluationSeqTwo)
    {
        $this->evaluationSeqTwo = $evaluationSeqTwo;
    }

    /**
     * @return mixed
     */
    public function getEvaluationSeqThree()
    {
        return $this->evaluationSeqThree;
    }

    /**
     * @param mixed $evaluationSeqThree
     */
    public function setEvaluationSeqThree($evaluationSeqThree)
    {
        $this->evaluationSeqThree = $evaluationSeqThree;
    }

    /**
     * @return mixed
     */
    public function getEvaluationSeqFourth()
    {
        return $this->evaluationSeqFourth;
    }

    /**
     * @param mixed $evaluationSeqFourth
     */
    public function setEvaluationSeqFourth($evaluationSeqFourth){
        $this->evaluationSeqFourth = $evaluationSeqFourth;
    }
}


<?php

namespace StudentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Inscription
 *
 * @ORM\Table(name="inscription")
 * @ORM\Entity(repositoryClass="StudentBundle\Repository\InscriptionRepository")
 */
class Inscription
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    function __construct() {
        $this->setDateDerniereAvance(new \DateTime(date('Y-m-d')));
        $this->setAvance(0);
    }

    function __toString() {
        return $this->getStudent()->getNom();
    }


    /**
     * @ORM\ManyToOne(targetEntity="StudentBundle\Entity\Student")
     * @Assert\NotBlank()
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity="StudentBundle\Entity\Classe")
     * @Assert\NotBlank()
     */
    private $classe;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    private $avance;

    /**
     * @var \Date
     *
     * @ORM\Column(type="date")
     */
    private $dateDerniereAvance;

    /**
     * @ORM\ManyToOne(targetEntity="ConfigBundle\Entity\Annee")
     */
    private $annee;

    /**
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(name="redoublant", type="boolean")
     */
    private $redoublant;

    protected $listeEvaluations = array();
    private $nbreAbsence;

    function getNbreAbsence() {
        return $this->nbreAbsence;
    }

    function setNbreAbsence($nbreAbsence) {
        $this->nbreAbsence = $nbreAbsence;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Inscription
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set student
     *
     * @param \StudentBundle\Entity\Student $student
     * @return Inscription
     */
    public function setStudent(\StudentBundle\Entity\Student $student = null) {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return \StudentBundle\Entity\Student 
     */
    public function getStudent() {
        return $this->student;
    }

    /**
     * Set classe
     *
     * @param \StudentBundle\Entity\Classe $classe
     * @return Inscription
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
     * Set avance
     *
     * @param integer $avance
     * @return Inscription
     */
    public function setAvance($avance) {
        $this->avance += $avance;

        return $this;
    }

    /**
     * Get avance
     *
     * @return integer 
     */
    public function getAvance() {
        return $this->avance;
    }

    /**
     * Set dateDerniereAvance
     *
     * @param \DateTime $dateDerniereAvance
     * @return Inscription
     */
    public function setDateDerniereAvance($dateDerniereAvance) {
        $this->dateDerniereAvance = $dateDerniereAvance;

        return $this;
    }

    /**
     * Get dateDerniereAvance
     *
     * @return \DateTime 
     */
    public function getDateDerniereAvance() {
        return $this->dateDerniereAvance;
    }

    /**
     * Set annee
     *
     * @param \ConfigBundle\Entity\Annee $annee
     * @return Inscription
     */
    public function setAnnee(\ConfigBundle\Entity\Annee $annee = null) {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return \ConfigBundle\Entity\Annee 
     */
    public function getAnnee() {
        return $this->annee;
    }

    public function setListeEvaluations($listeEvaluations) {
        $this->listeEvaluations = $listeEvaluations;
    }

    public function getListeEvaluations() {
        return $this->listeEvaluations;
    }

    /**
     * @return mixed
     */
    public function getRedoublant()
    {
        return $this->redoublant;
    }

    /**
     * @param mixed $redoublant
     */
    public function setRedoublant($redoublant)
    {
        $this->redoublant = $redoublant;
    }
}
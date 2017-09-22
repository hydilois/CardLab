<?php

namespace StudentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Absence
 *
 * @ORM\Table(name="absence")
 * @ORM\Entity(repositoryClass="StudentBundle\Repository\AbsenceRepository")
 */
class Absence
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function __construct() {
        $this->setNbreAbsence(0);
    }

    /**
     * @ORM\ManyToOne(targetEntity="StudentBundle\Entity\Inscription")
     * @Assert\NotBlank()
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity="NoteBundle\Entity\Sequence")
     * @Assert\NotBlank()
     */
    private $sequence;

    /**
     * @ORM\ManyToOne(targetEntity="ConfigBundle\Entity\Annee")
     * @Assert\NotBlank()
     */
    private $anneeScolaire;

    /**
     * @var float
     *
     * @ORM\Column(type="integer")
     */
    private $nbreAbsence;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set nbreAbscence
     *
     * @param integer $nbreAbscence
     * @return Absence
     */
    public function setNbreAbsence($nbreAbsence) {
        $this->nbreAbsence = $nbreAbsence;

        return $this;
    }
    
    /**
     * Set nbreAbscence
     *
     * @param integer $nbreAbscence
     * @return Absence
     */
    public function setUpNbreAbsence($nbreAbsence) {
        $this->nbreAbsence += $nbreAbsence;

        return $this;
    }

    /**
     * Get nbreAbscence
     *
     * @return integer 
     */
    public function getNbreAbsence() {
        return $this->nbreAbsence;
    }

    /**
     * Set student
     *
     * @param \StudentBundle\Entity\Inscription $student
     * @return Absence
     */
    public function setStudent(\StudentBundle\Entity\Inscription $student = null) {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return \StudentBundle\Entity\Inscription 
     */
    public function getStudent() {
        return $this->student;
    }

    /**
     * Set sequence
     *
     * @param \NoteBundle\Entity\Sequence $sequence
     * @return Absence
     */
    public function setSequence(\NoteBundle\Entity\Sequence $sequence = null) {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence
     *
     * @return \NoteBundle\Entity\Sequence 
     */
    public function getSequence() {
        return $this->sequence;
    }

    /**
     * Set anneeScolaire
     *
     * @param \ConfigBundle\Entity\Annee $anneeScolaire
     * @return Absence
     */
    public function setAnneeScolaire(\ConfigBundle\Entity\Annee $anneeScolaire = null) {
        $this->anneeScolaire = $anneeScolaire;

        return $this;
    }

    /**
     * Get anneeScolaire
     *
     * @return \ConfigBundle\Entity\Annee 
     */
    public function getAnneeScolaire() {
        return $this->anneeScolaire;
    }
}


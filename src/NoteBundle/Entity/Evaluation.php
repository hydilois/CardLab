<?php

namespace NoteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Evaluation
 *
 * @ORM\Table(name="evaluation")
 * @ORM\Entity(repositoryClass="NoteBundle\Repository\EvaluationRepository")
 */
class Evaluation {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="StudentBundle\Entity\Student")
     * @Assert\NotBlank()
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity="StudentBundle\Entity\Classe")
     */
    private $classe;

    /**
     * @ORM\ManyToOne(targetEntity="NoteBundle\Entity\Sequence")
     * @Assert\NotBlank()
     */
    private $sequence;

    /**
     * @ORM\ManyToOne(targetEntity="MatiereBundle\Entity\Matiere")
     * @Assert\NotBlank()
     */
    private $matiere;

    /**
     * @var float
     *
     * @ORM\Column(name="note", type="float")
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="ConfigBundle\Entity\Annee")
     */
    private $annee;
    private $index;

    function getIndex() {
        return $this->index;
    }

    function setIndex($index) {
        $this->index = $index;
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
     * Set note
     *
     * @param integer $note
     * @return Evaluation
     */
    public function setNote($note) {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return integer 
     */
    public function getNote() {
        return $this->note;
    }

    /**
     * Set student
     *
     * @param \StudentBundle\Entity\Student $student
     * @return Evaluation
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
     * Set sequence
     *
     * @param \NoteBundle\Entity\Sequence $sequence
     * @return Evaluation
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

    public function __toString() {
        return $this->getNom();
    }

    /**
     * Set matiere
     *
     * @param \MatiereBundle\Entity\Matiere $matiere
     * @return Evaluation
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
     * Set annee
     *
     * @param \ConfigBundle\Entity\Annee $annee
     * @return Evaluation
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

    /**
     * Set classe
     *
     * @param \StudentBundle\Entity\Classe $classe
     * @return Evaluation
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

}

<?php

namespace NoteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sequence
 *
 * @ORM\Table(name="sequence")
 * @ORM\Entity(repositoryClass="NoteBundle\Repository\SequenceRepository")
 */
class Sequence
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function __toString() {
        return $this->getNom();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     */
    private $nom;

    private $listeCategories;

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
     * @return Sequence
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
     * Set listeCategories
     *
     * @param boolean $listeCategories
     * @return Inscription
     */
    public function setListeCategories($listeCategories)
    {
        $this->listeCategories = $listeCategories;

        return $this;
    }

    /**
     * Get listeCategories
     *
     * @return boolean
     */
    public function getListeCategories()
    {
        return $this->listeCategories;
    }
}


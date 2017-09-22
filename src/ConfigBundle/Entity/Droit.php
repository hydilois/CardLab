<?php

namespace ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Droit
 *
 * @ORM\Table(name="droit")
 * @ORM\Entity(repositoryClass="ConfigBundle\Repository\DroitRepository")
 */
class Droit
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
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $rightToken;

    /**
     * @var text
     * 
     * @ORM\Column(type="text", length=255)
     * @Assert\NotBlank()
     */
    private $membreDuDroit;

    /**
     * @var text
     * 
     * @ORM\Column(type="text", length=255)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * Set nom
     *
     * @param string $nom
     * @return Droit
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
     * Set membreDuDroit
     *
     * @param string $membreDuDroit
     * @return Droit
     */
    public function setMembreDuDroit($membreDuDroit) {
        $this->membreDuDroit = $membreDuDroit;

        return $this;
    }

    /**
     * Get membreDuDroit
     *
     * @return string 
     */
    public function getMembreDuDroit() {
        return $this->membreDuDroit;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Droit
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }


    /**
     * Set rightToken
     *
     * @param string $rightToken
     * @return Droit
     */
    public function setRightToken($rightToken)
    {
        $this->rightToken = $rightToken;
    
        return $this;
    }

    /**
     * Get rightToken
     *
     * @return string 
     */
    public function getRightToken()
    {
        return $this->rightToken;
    }
}

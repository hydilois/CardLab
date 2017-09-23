<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * Utilisateur
 *
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UtilisateurRepository")
 */
class Utilisateur extends BaseUser
{
    public function __toString() {
        return ($this->getNom() != '') ? $this->getNom() : 'Pas de Nom';
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    private $anciennete;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $matricule;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $grade;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $diplomes;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telephone;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $level;

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
     * @return User
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
     * Set anciennete
     *
     * @param integer $anciennete
     * @return User
     */
    public function setAnciennete($anciennete) {
        $this->anciennete = $anciennete;

        return $this;
    }

    /**
     * Get anciennete
     *
     * @return integer 
     */
    public function getAnciennete() {
        return $this->anciennete;
    }

    /**
     * Set matricule
     *
     * @param string $matricule
     * @return User
     */
    public function setMatricule($matricule) {
        $this->matricule = $matricule;

        return $this;
    }

    /**
     * Get matricule
     *
     * @return string 
     */
    public function getMatricule() {
        return $this->matricule;
    }

    /**
     * Set grade
     *
     * @param string $grade
     * @return User
     */
    public function setGrade($grade) {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return string 
     */
    public function getGrade() {
        return $this->grade;
    }

    /**
     * Set diplomes
     *
     * @param string $diplomes
     * @return User
     */
    public function setDiplomes($diplomes) {
        $this->diplomes = $diplomes;

        return $this;
    }

    /**
     * Get diplomes
     *
     * @return string 
     */
    public function getDiplomes() {
        return $this->diplomes;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return User
     */
    public function setTelephone($telephone) {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone() {
        return $this->telephone;
    }


    /**
     * Set level
     *
     * @param string $level
     * @return User
     */
    public function setLevel($level)
    {
        $this->level = $level;
    
        return $this;
    }

    /**
     * Get level
     *
     * @return string 
     */
    public function getLevel()
    {
        return $this->level;
    }
}

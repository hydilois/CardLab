<?php

namespace ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Annee
 *
 * @ORM\Table(name="annee")
 * @ORM\Entity(repositoryClass="ConfigBundle\Repository\AnneeRepository")
 */
class Annee
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
     * @var int
     *
     * @ORM\Column(name="annee_debut", type="integer")
     */
    private $anneeDebut;

    /**
     * @var int
     *
     * @ORM\Column(name="annee_fin", type="integer")
     */
    private $anneeFin;

    /**
     * @var string
     *
     * @ORM\Column(name="annee_scolaire", type="string", length=255, unique=true)
     */
    private $anneeScolaire;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_annee_en_cour", type="boolean")
     */
    private $isAnneeEnCour;

    public function __toString(){
        return $this->anneeScolaire;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set anneeScolaire
     *
     * @param string $anneeScolaire
     *
     * @return Annee
     */
    public function setAnneeScolaire($anneeScolaire)
    {
        $this->anneeScolaire = $anneeScolaire;

        return $this;
    }

    /**
     * Get anneeScolaire
     *
     * @return string
     */
    public function getAnneeScolaire()
    {
        return $this->anneeScolaire;
    }

    /**
     * Set anneeDebut
     *
     * @param integer $anneeDebut
     *
     * @return Annee
     */
    public function setAnneeDebut($anneeDebut)
    {
        $this->anneeDebut = $anneeDebut;

        return $this;
    }

    /**
     * Get anneeDebut
     *
     * @return integer
     */
    public function getAnneeDebut()
    {
        return $this->anneeDebut;
    }

    /**
     * Set anneeFin
     *
     * @param integer $anneeFin
     *
     * @return Annee
     */
    public function setAnneeFin($anneeFin)
    {
        $this->anneeFin = $anneeFin;

        return $this;
    }

    /**
     * Get anneeFin
     *
     * @return integer
     */
    public function getAnneeFin()
    {
        return $this->anneeFin;
    }

    /**
     * Set isAnneeEnCour
     *
     * @param boolean $isAnneeEnCour
     *
     * @return Annee
     */
    public function setIsAnneeEnCour($isAnneeEnCour)
    {
        $this->isAnneeEnCour = $isAnneeEnCour;

        return $this;
    }

    /**
     * Get isAnneeEnCour
     *
     * @return boolean
     */
    public function getIsAnneeEnCour()
    {
        return $this->isAnneeEnCour;
    }
}

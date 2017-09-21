<?php

namespace ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Constante
 *
 * @ORM\Table(name="constante")
 * @ORM\Entity(repositoryClass="ConfigBundle\Repository\ConstanteRepository")
 */
class Constante
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
    private $ville;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nomFrancais;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nomAnglais;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $deviseFrancais;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $deviseAnglais;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $boitePostal;

    /**
     * @ORM\OneToOne(targetEntity="Logo",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="logo_id", referencedColumnName="id", nullable=true)
     *  @Assert\Valid()
     */
    private $logo;

    /**
     * @ORM\OneToOne(targetEntity="Annee")
     * @Assert\NotBlank()
     */
    private $anneeEnCour;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set nomFrancais
     *
     * @param string $nomFrancais
     * @return Ecole
     */
    public function setNomFrancais($nomFrancais) {
        $this->nomFrancais = $nomFrancais;

        return $this;
    }

    /**
     * Get nomFrancais
     *
     * @return string 
     */
    public function getNomFrancais() {
        return $this->nomFrancais;
    }

    /**
     * Set nomAnglais
     *
     * @param string $nomAnglais
     * @return Ecole
     */
    public function setNomAnglais($nomAnglais) {
        $this->nomAnglais = $nomAnglais;

        return $this;
    }

    /**
     * Get nomAnglais
     *
     * @return string 
     */
    public function getNomAnglais() {
        return $this->nomAnglais;
    }

    /**
     * Set deviseFrancais
     *
     * @param string $deviseFrancais
     * @return Ecole
     */
    public function setDeviseFrancais($deviseFrancais) {
        $this->deviseFrancais = $deviseFrancais;

        return $this;
    }

    /**
     * Get deviseFrancais
     *
     * @return string 
     */
    public function getDeviseFrancais() {
        return $this->deviseFrancais;
    }

    /**
     * Set deviseAnglais
     *
     * @param string $deviseAnglais
     * @return Ecole
     */
    public function setDeviseAnglais($deviseAnglais) {
        $this->deviseAnglais = $deviseAnglais;

        return $this;
    }

    /**
     * Get deviseAnglais
     *
     * @return string 
     */
    public function getDeviseAnglais() {
        return $this->deviseAnglais;
    }

    /**
     * Set boitePostal
     *
     * @param string $boitePostal
     * @return Ecole
     */
    public function setBoitePostal($boitePostal) {
        $this->boitePostal = $boitePostal;

        return $this;
    }

    /**
     * Get boitePostal
     *
     * @return string 
     */
    public function getBoitePostal() {
        return $this->boitePostal;
    }

    /**
     * Set anneeEnCour
     *
     * @param \School\ConfigBundle\Entity\Annee $anneeEnCour
     * @return Ecole
     */
    public function setAnneeEnCour(\School\ConfigBundle\Entity\Annee $anneeEnCour = null) {
        $this->anneeEnCour = $anneeEnCour;

        return $this;
    }

    /**
     * Get anneeEnCour
     *
     * @return \School\ConfigBundle\Entity\Annee 
     */
    public function getAnneeEnCour() {
        return $this->anneeEnCour;
    }


    /**
     * Set logo
     *
     * @param \School\ConfigBundle\Entity\Logo $logo
     * @return Ecole
     */
    public function setLogo(\School\ConfigBundle\Entity\Logo $logo = null)
    {
        $this->logo = $logo;
    
        return $this;
    }

    /**
     * Get logo
     *
     * @return \School\ConfigBundle\Entity\Logo 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return Ecole
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
    
        return $this;
    }

    /**
     * Get ville
     *
     * @return string 
     */
    public function getVille()
    {
        return $this->ville;
    }
}


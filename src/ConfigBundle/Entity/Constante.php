<?php

namespace ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @ORM\OneToOne(targetEntity="StudentBundle\Entity\Image",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="logo_id", referencedColumnName="id", nullable=true)
     */
    private $logo;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string 
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Constante
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

    /**
     * Set nomFrancais
     *
     * @param string $nomFrancais
     *
     * @return Constante
     */
    public function setNomFrancais($nomFrancais)
    {
        $this->nomFrancais = $nomFrancais;

        return $this;
    }

    /**
     * Get nomFrancais
     *
     * @return string
     */
    public function getNomFrancais()
    {
        return $this->nomFrancais;
    }

    /**
     * Set nomAnglais
     *
     * @param string $nomAnglais
     *
     * @return Constante
     */
    public function setNomAnglais($nomAnglais)
    {
        $this->nomAnglais = $nomAnglais;

        return $this;
    }

    /**
     * Get nomAnglais
     *
     * @return string
     */
    public function getNomAnglais()
    {
        return $this->nomAnglais;
    }

    /**
     * Set deviseFrancais
     *
     * @param string $deviseFrancais
     *
     * @return Constante
     */
    public function setDeviseFrancais($deviseFrancais)
    {
        $this->deviseFrancais = $deviseFrancais;

        return $this;
    }

    /**
     * Get deviseFrancais
     *
     * @return string
     */
    public function getDeviseFrancais()
    {
        return $this->deviseFrancais;
    }

    /**
     * Set deviseAnglais
     *
     * @param string $deviseAnglais
     *
     * @return Constante
     */
    public function setDeviseAnglais($deviseAnglais)
    {
        $this->deviseAnglais = $deviseAnglais;

        return $this;
    }

    /**
     * Get deviseAnglais
     *
     * @return string
     */
    public function getDeviseAnglais()
    {
        return $this->deviseAnglais;
    }

    /**
     * Set boitePostal
     *
     * @param string $boitePostal
     *
     * @return Constante
     */
    public function setBoitePostal($boitePostal)
    {
        $this->boitePostal = $boitePostal;

        return $this;
    }

    /**
     * Get boitePostal
     *
     * @return string
     */
    public function getBoitePostal()
    {
        return $this->boitePostal;
    }

    /**
     * Set logo
     *
     * @param \StudentBundle\Entity\Image $logo
     *
     * @return Constante
     */
    public function setLogo(\StudentBundle\Entity\Image $logo = null)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return \StudentBundle\Entity\Image
     */
    public function getLogo()
    {
        return $this->logo;
    }
}

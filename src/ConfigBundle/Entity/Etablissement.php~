<?php

namespace ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Etablissement
 *
 * @ORM\Table(name="etablissement")
 * @ORM\Entity(repositoryClass="ConfigBundle\Repository\EtablissementRepository")
 */
class Etablissement
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
     * @ORM\Column(name="ville", type="string", length=100, unique=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="nomFrancais", type="string", length=255, unique=true)
     */
    private $nomFrancais;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $nomAnglais;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $deviseFrancais;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $deviseAnglais;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $boitePostal;

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
     * Set ville
     *
     * @param string $ville
     *
     * @return Etablissement
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
     * @return Etablissement
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
     * @return Etablissement
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
     * @return Etablissement
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
     * @return Etablissement
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
     * @return Etablissement
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
}

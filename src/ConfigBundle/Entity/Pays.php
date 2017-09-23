<?php

namespace ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pays
 *
 * @ORM\Table(name="pays")
 * @ORM\Entity(repositoryClass="ConfigBundle\Repository\PaysRepository")
 */
class Pays
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
    private $paysFrancais;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $paysAnglais;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $ministereFrancais;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $ministereAnglais;

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
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }


    /**
     * Set paysFrancais
     *
     * @param string $paysFrancais
     * @return Constante
     */
    public function setPaysFrancais($paysFrancais)
    {
        $this->paysFrancais = $paysFrancais;
    
        return $this;
    }

    /**
     * Get paysFrancais
     *
     * @return string 
     */
    public function getPaysFrancais()
    {
        return $this->paysFrancais;
    }

    /**
     * Set paysAnglais
     *
     * @param string $paysAnglais
     * @return Constante
     */
    public function setPaysAnglais($paysAnglais)
    {
        $this->paysAnglais = $paysAnglais;
    
        return $this;
    }

    /**
     * Get paysAnglais
     *
     * @return string 
     */
    public function getPaysAnglais()
    {
        return $this->paysAnglais;
    }

    /**
     * Set ministereFrancais
     *
     * @param string $ministereFrancais
     * @return Constante
     */
    public function setMinistereFrancais($ministereFrancais)
    {
        $this->ministereFrancais = $ministereFrancais;
    
        return $this;
    }

    /**
     * Get ministereFrancais
     *
     * @return string 
     */
    public function getMinistereFrancais()
    {
        return $this->ministereFrancais;
    }

    /**
     * Set ministereAnglais
     *
     * @param string $ministereAnglais
     * @return Constante
     */
    public function setMinistereAnglais($ministereAnglais)
    {
        $this->ministereAnglais = $ministereAnglais;
    
        return $this;
    }

    /**
     * Get ministereAnglais
     *
     * @return string 
     */
    public function getMinistereAnglais()
    {
        return $this->ministereAnglais;
    }

    /**
     * Set deviseFrancais
     *
     * @param string $deviseFrancais
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
}


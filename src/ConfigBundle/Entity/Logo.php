<?php

namespace ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logo
 *
 * @ORM\Table(name="logo")
 * @ORM\Entity(repositoryClass="ConfigBundle\Repository\LogoRepository")
 */
class Logo
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}


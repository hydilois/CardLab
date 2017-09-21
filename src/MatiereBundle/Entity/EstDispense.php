<?php

namespace MatiereBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstDispense
 *
 * @ORM\Table(name="est_dispense")
 * @ORM\Entity(repositoryClass="MatiereBundle\Repository\EstDispenseRepository")
 */
class EstDispense
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


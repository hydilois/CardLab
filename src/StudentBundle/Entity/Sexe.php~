<?php

namespace StudentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sexe
 *
 * @ORM\Table(name="sexe")
 * @ORM\Entity(repositoryClass="StudentBundle\Repository\SexeRepository")
 */
class Sexe
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
         * @ORM\Column(type="string", length=255)
         * @Assert\NotBlank()
         */
        private $nom;

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
         * @return Sexe
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
}


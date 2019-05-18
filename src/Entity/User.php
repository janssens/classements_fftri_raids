<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Athlete", inversedBy="user")
     */
    private $athlete;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public function __toString()
    {
        return ($this->getAthlete())? $this->getAthlete()->getFirstName() : $this->getUsername();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAthlete(): ?Athlete
    {
        return $this->athlete;
    }

    public function setAthlete(Athlete $athlete): self
    {
        $this->athlete = $athlete;

        return $this;
    }

    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

    public function getDisplayName(): string
    {
        return ($this->getAthlete()) ? $this->getAthlete()->getFirstname() : $this->getUsername();
    }
}
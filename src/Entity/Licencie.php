<?php
/**
 * Created by PhpStorm.
 * User: gjanssens
 * Date: 17/10/18
 * Time: 07:55
 */
//src//Entity/Licencie.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="licencie")
 */
class Licencie
{
    const TYPE_A = 1;
    const TYPE_B = 2;
    const TYPE_C = 3;
    const TYPE_D = 4;
    const TYPE_H = 7;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastname;

    /**
     * @ORM\Column(type="integer")
     */
    private $gender;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="date")
     */
    private $dob;

    /**
     * @ORM\Column(type="datetime")
     */
    private $last_validation_date;

    /**
     * Many Licencies have Many Equipes.
     * @ORM\ManyToMany(targetEntity="Equipe", inversedBy="licencies")
     * @ORM\JoinTable(name="licencies_equipes")
     */
    private $equipes;

    /**
     * @ORM\ManyToOne(targetEntity="Club", inversedBy="licencies")
     * @ORM\JoinColumn(name="club_id", referencedColumnName="id")
     */
    private $club;

    /**
     * @ORM\ManyToOne(targetEntity="Ligue", inversedBy="licencies")
     * @ORM\JoinColumn(name="ligue_id", referencedColumnName="id")
     */
    private $ligue;

}
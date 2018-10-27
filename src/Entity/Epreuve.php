<?php
/**
 * Created by PhpStorm.
 * User: gjanssens
 * Date: 17/10/18
 * Time: 07:55
 */
//src//Entity/Epreuve.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="epreuve")
 */
class Epreuve
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal")
     */
    private $lat;

    /**
     * @ORM\Column(type="decimal")
     */
    private $long;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="Equipe", mappedBy="epreuve")
     */
    private $equipes;


}
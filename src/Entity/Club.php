<?php
/**
 * Created by PhpStorm.
 * User: gjanssens
 * Date: 17/10/18
 * Time: 07:55
 */
//src//Entity/Club.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="club")
 */
class Club
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
     * @ORM\OneToMany(targetEntity="Licencie", mappedBy="club")
     */
    private $licencies;
}
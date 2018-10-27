<?php
/**
 * Created by PhpStorm.
 * User: gjanssens
 * Date: 17/10/18
 * Time: 07:55
 */
//src//Entity/Equipe.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="equipe")
 */
class Equipe
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
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\Column(type="integer")
     */
    private $gender;

    /**
     * @ORM\ManyToOne(targetEntity="Epreuve", inversedBy="equipes")
     * @ORM\JoinColumn(name="epreuve_id", referencedColumnName="id")
     */
    private $epreuve;

    /**
     * Many Equipes have Many Licencies.
     * @ORM\ManyToMany(targetEntity="Licencie", mappedBy="equipes")
     */
    private $licencies;

}
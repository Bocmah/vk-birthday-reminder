<?php

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="observers")
 **/
class Observer
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="integer")
     */
    private $vkId;

    /**
     * @Column(type="string")
     */
    private $firstName;

    /**
     * @Column(type="string")
     */
    private $lastName;

    /**
     * @OneToMany(targetEntity="Observee", mappedBy="observer")
     */
    private $observees;

    public function __construct()
    {
        $this->observees = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getVkId()
    {
        return $this->vkId;
    }

    public function setVkId($vkId)
    {
        $this->vkId = $vkId;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
}
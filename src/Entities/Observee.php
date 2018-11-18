<?php

/**
 * @Entity @Table(name="observees")
 **/
class Observee
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
     * @Column(type="date")
     */
    private $birthday;

    /**
     * @ManyToOne(targetEntity="Observer", inversedBy="observees")
     */
    private $observer;

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

    public function getBirthday()
    {
        return $this->birthday;
    }
    
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }
}

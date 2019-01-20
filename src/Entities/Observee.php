<?php

namespace VkBirthdayReminder\Entities;

/**
 * @Entity @Table(name="observees")
 **/
class Observee
{
    /**
     * @var int
     *
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @var int
     *
     * @Column(type="integer", name="vk_id")
     */
    private $vkId;

    /**
     * @var string
     *
     * @Column(type="string", name="first_name")
     */
    private $firstName;

    /**
     * @var string
     *
     * @Column(type="string", name="last_name")
     */
    private $lastName;

    /**
     * @Column(type="date")
     */
    private $birthday;

    /**
     * @var Observer
     *
     * @ManyToOne(targetEntity="Observer", inversedBy="observees")
     */
    private $observer;

    public function getId(): int
    {
        return $this->id;
    }

    public function getVkId(): int
    {
        return $this->vkId;
    }

    public function setVkId(int $vkId)
    {
        $this->vkId = $vkId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    public function getBirthday(): \DateTime
    {
        return $this->birthday;
    }
    
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    public function getObserver(): Observer
    {
        return $this->observer;
    }

    public function setObserver(Observer $observer)
    {
        $this->observer = $observer;
    }
}

<?php

namespace VkBirthdayReminder\Entities;

use \Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity @Table(name="observers")
 **/
class Observer
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
     * @var bool
     *
     * @Column(type="boolean, name="is_notifiable")
     */
    private $isNotifiable = true;

    /**
     * @var Observee[]|ArrayCollection
     *
     * @OneToMany(targetEntity="Observee", mappedBy="observer")
     */
    private $observees;

    public function __construct()
    {
        $this->observees = new ArrayCollection();
    }

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

    public function setFirstName(string $firstName)
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

    public function getIsNotifiable(): bool
    {
        return $this->isNotifiable;
    }

    public function setIsNotifiable(bool $isNotifiable)
    {
        $this->isNotifiable = $isNotifiable;
    }

    public function getObservees(): Collection
    {
        return $this->observees;
    }
}
<?php

namespace VkBirthdayReminder\Helpers;

/**
 * The class is used to decompose the message and retrieve necessary parts from it.
 */
class ObserveeDataRetriever
{
    /**
     * @var UserRetriever
     */
    protected $userRetriever;

    public function __construct(UserRetriever $userRetriever)
    {
        $this->userRetriever = $userRetriever;
    }

    /**
     * @param string $message
     * @return string
     */
    public function getBirthdayFromMessage(string $message): string
    {
        $messageParts = explode(" ", $message);
        [$day, $month, $year] = explode(".",$messageParts[2]);
        $birthday = "{$year}-{$month}-{$day}";

        return $birthday;
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function getVkUserObjectFromMessage(string $message)
    {
        $vkUserId = $this->getVkUserIdFromMessage($message);

        return $this->userRetriever->getUser($vkUserId, true);
    }

    /**
     * @param string $message
     * @return string
     */
    public function getVkUserIdFromMessage(string $message): string
    {
        $messageParts = explode(" ", $message);

        return $messageParts[1];
    }
}
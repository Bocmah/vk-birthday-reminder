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
        $messagePartsCount = count($messageParts);

        if ($messagePartsCount !== 3) {
            throw new \LengthException(
                "The input message consists of {$messagePartsCount} parts instead of required 3 parts."
            );
        }

        $explodedMessageParts = explode('.', $messageParts[2]);
        $explodedMessagePartsCount = count($explodedMessageParts);

        if ($explodedMessagePartsCount !== 3) {
            throw new \LengthException(
              "Could not explode the birthday part of the message into 3 parts. Got {$explodedMessagePartsCount} instead."
            );
        }

        [$day, $month, $year] = $explodedMessageParts;
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
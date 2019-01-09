<?php

namespace VkBirthdayReminder\Helpers;

/**
 * Class used to send messages to VK users making use of VK API.
 */
class MessageSender
{
    /**
     * Maximum message length that VK api supports.
     */
    const MAX_MESSAGE_LENGTH = 4096;

    /**
     * Send a message to a user of $userId
     *
     * @param string $txt
     * @param $userId
     * @return bool
     */
    public function send(string $txt, $userId)
    {
        $requestParams = [
            "user_id" => $userId,
            "message" => $txt,
            "access_token" => getenv("VK_TOKEN"),
            "v" => getenv("VK_API_VERSION"),
            "fields" => "first_name,last_name"
        ];
        file_get_contents(
            "https://api.vk.com/method/messages.send?" . http_build_query($requestParams)
        );

        return true;
    }
}
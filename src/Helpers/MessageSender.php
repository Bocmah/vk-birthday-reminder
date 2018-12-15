<?php

namespace VkBirthdayReminder\Helpers;

/**
 * Class used to send messages to VK users making use of VK API.
 */
class MessageSender
{
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
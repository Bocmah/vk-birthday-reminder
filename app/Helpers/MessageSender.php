<?php

namespace VkBirthdayReminder\Helpers;

class MessageSender
{
    /**
     * Send a message to a user of $userId
     *
     * @param string $txt
     * @param $userId
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
    }
}
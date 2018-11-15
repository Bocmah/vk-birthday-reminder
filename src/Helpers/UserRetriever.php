<?php

namespace VkBirthdayReminder\Helpers;

class UserRetriever
{
    /**
     * Retrieve VK user by $id
     * Returns JSON decoded object or associative array if $returnAssoc is true
     *
     * @param $id
     * @param bool $returnAssoc
     * @return bool|string
     */
    public function getUser($id, bool $returnAssoc = false)
    {
        $requestParams = [
          "user_ids" => $id,
          "v" => getenv("VK_API_VERSION"),
          "access_token" => getenv("VK_TOKEN")
        ];

        $result = file_get_contents(
            "https://api.vk.com/method/users.get?" . http_build_query($requestParams)
        );

        return json_decode($result, $returnAssoc);
    }
}
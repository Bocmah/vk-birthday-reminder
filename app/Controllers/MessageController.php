<?php

namespace VkBirthdayReminder\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController
{
    public function store(Request $request)
    {
        $data = json_decode($request->getContent());

        switch ($data->type) {
            case "message_new":
                $requestParams = [
                  "user_id" => $data->object->from_id,
                  "message" => "Test",
                  "access_token" => getenv("VK_TOKEN"),
                  "v" => "5.85"
                ];
                file_get_contents(
                    "https://api.vk.com/method/messages.send?" . http_build_query($requestParams)
                );

                return new Response("ok");
                break;
        }
    }
}
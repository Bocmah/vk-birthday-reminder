<?php

namespace VkBirthdayReminder\Database;

class Connection
{
    public static function make(array $config)
    {
        try {
            return new \PDO(
                $config["database"]["connection"].";dbname=".$config["database"]["name"].
                ";charset=".$config["database"]["encoding"],
                $config["database"]["username"],
                $config["database"]["password"],
                $config["database"]["options"]
            );
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }
}
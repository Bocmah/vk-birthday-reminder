<?php

namespace VkBirthdayReminder;

/**
 * Contains patterns of commands, which can be handled by bot.
 */
final class CommandPatterns
{
    /**
     * Add a new birthday to the database.
     */
    const BIRTHDAY_ADD = "/Add\s\S+\s\d\d\.\d\d\.\d{4}/i";
}
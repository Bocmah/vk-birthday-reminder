<?php

namespace VkBirthdayReminder\Helpers;

use VkBirthdayReminder\Commands;

/**
 * Class responsible for parsing the specific text and determining whether it matches one of the available patterns.
 */
class CommandParser
{
    /**
     * @var array  Available commands
     */
    protected $commandPatterns = [
      Commands::ADD => "/Add\s\S+\s\d\d\.\d\d\.\d{4}/i",
      Commands::LIST => "/list/i",
      Commands::UPDATE => "/update\s\S+\s\d\d\.\d\d\.\d{4}/i",
      Commands::DELETE => "/delete\s\S+/i",
      Commands::HELP => "/help/i"
    ];

    /**
     * Find out whether $text is a known command. If this is the case, return a command name.
     * Return unknown command name otherwise.
     *
     * @param string $text
     * @return string
     */
    public function parse(string $text): string
    {
        foreach ($this->commandPatterns as $command => $pattern) {
            if (preg_match($pattern,$text)) return $command;
        }

        return Commands::UNKNOWN;
    }
}
<?php

namespace VkBirthdayReminder\Commands;

/**
 * Determines the behaviour that all Command classes must implement.
 */
interface CommandInterface
{
    /**
     * Execute the command
     */
    public function execute();
}
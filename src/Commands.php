<?php

namespace VkBirthdayReminder;

/**
 * Contains all commands which are recognizable by the bot.
 */
final class Commands
{
    /**
     * Add a new observee.
     */
    const ADD = 'add';

    /**
     * Update an existing observee.
     */
    const UPDATE = 'update';

    /**
     * List all observees for observer who made a request.
     */
    const LIST = 'list';

    /**
     * List all commands recognizable by the bot and its' short descriptions.
     */
    const HELP = 'help';

    /**
     * This label is assigned to any command which is unrecognizable by the bot.
     */
    const UNKNOWN = 'unknown';
}
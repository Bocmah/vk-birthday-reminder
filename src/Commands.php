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
     * This label is assigned to any command which is unrecognizable by the bot.
     */
    const UNKNOWN = 'unknown';

    /**
     * List all observees for observer who made a request.
     */
    const LIST = 'list';
}
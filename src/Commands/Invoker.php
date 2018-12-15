<?php

namespace VkBirthdayReminder\Commands;

/**
 * Used to invoke the command provided by executing setCommand method.
 */
class Invoker
{
    /**
     * @var CommandInterface
     */
    private $command;

    /**
     * @param CommandInterface $cmd
     */
    public function setCommand(CommandInterface $cmd)
    {
        $this->command = $cmd;
    }

    /**
     * Execute the command.
     */
    public function run()
    {
        $this->command->execute();
    }
}
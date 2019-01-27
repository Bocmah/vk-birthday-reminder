<?php

namespace VkBirthdayReminder\Tests;

use PHPUnit\Framework\TestCase;
use VkBirthdayReminder\Helpers\CommandParser;
use VkBirthdayReminder\Commands;

class CommandParserTest extends TestCase
{
    /**
     * @test
     * @dataProvider commandsProvider
     */
    public function retrievesCommandsCorrectly($inputCommand, $expectedOutputCommand)
    {
        $parser = new CommandParser();
        $this->assertEquals($expectedOutputCommand, $parser->parse($inputCommand));
    }

    public function commandsProvider()
    {
        return [
            ['Add 1 13.10.1996', Commands::ADD],
            ['list', Commands::LIST],
            ['update 823452 12.12.1990', Commands::UPDATE],
            ['delete 2523', Commands::DELETE],
            ['nOtIfy', Commands::NOTIFY],
            ['help', Commands::HELP],
            ['gklasdrei', Commands::UNKNOWN]
        ];
    }
}
<?php

namespace VkBirthdayReminder\Tests;

use PHPUnit\Framework\TestCase;
use VkBirthdayReminder\Helpers\ObserveeDataRetriever;
use VkBirthdayReminder\Helpers\UserRetriever;

class ObserveeDataRetrieverTest extends TestCase
{
    /**
     * @var ObserveeDataRetriever
     */
    protected $observeeDataRetriever;

    protected function setUp()
    {
        $this->observeeDataRetriever = new ObserveeDataRetriever(
            $this->createMock(UserRetriever::class)
        );
    }

    public function getBirthdayFromMessageExceptionsProvider()
    {
        return [
            ['add 1'],
            ['add 1 13.10']
        ];
    }

    /**
     * @test
     * @dataProvider getBirthdayFromMessageExceptionsProvider
     * @param $message
     */
    public function getBirthdayFromMessageCorrectlyHandlesExceptions($message)
    {
        $this->expectException(\LengthException::class);
        $this->observeeDataRetriever->getBirthdayFromMessage($message);
    }

    /**
     * @test
     */
    public function retrievesBirthdayFromMessageCorrectly()
    {
        $message = 'add 1 13.10.1996';
        $actual = $this->observeeDataRetriever->getBirthdayFromMessage($message);
        $expected = '1996-10-13';

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getVkUserIdFromMessageCorrectlyHandlesMessageOfLessThanRequiredLength()
    {
        $message = 'add';

        $this->expectException(\LengthException::class);

        $this->observeeDataRetriever->getVkUserIdFromMessage($message);
    }

    /**
     * @test
     */
    public function correctlyRetrievesVkUserIdFromMessage()
    {
        $message = 'add 12345';
        $expected = '12345';
        $actual = $this->observeeDataRetriever->getVkUserIdFromMessage($message);

        $this->assertEquals($expected, $actual);
    }
}
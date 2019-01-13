<?php

namespace VkBirthdayReminder\Tests;

use PHPUnit\Framework\TestCase;
use VkBirthdayReminder\Helpers\ObserveeDataRetriever;
use VkBirthdayReminder\Helpers\UserRetriever;

class ObserveeDataRetrieverTest extends TestCase
{
    /**
     * @test
     */
    public function getBirthdayFromMessageHandlesMessageWhichDoesNotContainRequiredNumberOfWordsCorrectly()
    {
        $userRetrieverStub = $this->createMock(UserRetriever::class);
        $observeeDataRetriever = new ObserveeDataRetriever($userRetrieverStub);
        $message = 'add 1';

        $this->expectException(\LengthException::class);

        $observeeDataRetriever->getBirthdayFromMessage($message);
    }

    /**
     * @test
     */
    public function getBirthdayFromMessagesHandlesIncorrectBirthdayPartCorrectly()
    {
        $userRetrieverStub = $this->createMock(UserRetriever::class);
        $observeeDataRetriever = new ObserveeDataRetriever($userRetrieverStub);
        $message = 'add 1 13.10';

        $this->expectException(\LengthException::class);

        $observeeDataRetriever->getBirthdayFromMessage($message);
    }

    /**
     * @test
     */
    public function retrievesBirthdayFromMessageCorrectly()
    {
        $userRetrieverStub = $this->createMock(UserRetriever::class);
        $observeeDataRetriever = new ObserveeDataRetriever($userRetrieverStub);
        $message = 'add 1 13.10.1996';
        $actual = $observeeDataRetriever->getBirthdayFromMessage($message);
        $expected = '1996-10-13';

        $this->assertEquals($expected, $actual);
    }
}
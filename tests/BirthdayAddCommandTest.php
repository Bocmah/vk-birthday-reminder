<?php

namespace VkBirthdayReminder\Tests;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VkBirthdayReminder\Commands\BirthdayAddCommand;
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\Helpers\ObserveeDataRetriever;
use VkBirthdayReminder\Helpers\UserRetriever;

class BirthdayAddCommandTest extends TestCase
{
    /**
     * @test
     */
    public function correctlyRespondsToInvalidDateOfBirth()
    {
        $msgStub = new \stdClass();
        $msgStub->from_id = 5;
        $msgStub->text = 'Add 532464241 32.09.2000';

        /** @var UserRetriever|MockObject $userRetrieverStub */
        $userRetrieverStub = $this->createMock(UserRetriever::class);

        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
                          ->method('send')
                          ->with(
                              "Обнаружены ошибки:\n\nДата неправильная. Она должна быть в формате DD.MM.YYYY. Например: 13.10.1996.\n",
                              5
                          );
        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);

        /** @var ObserveeDataRetriever|MockObject $observeeRetrieverStub */
        $observeeRetrieverStub = $this->createMock(ObserveeDataRetriever::class);
        $observeeRetrieverStub->method('getBirthdayFromMessage')
            ->willReturn('2000-09-32');


        $birthdayAddCommand = new BirthdayAddCommand(
            $msgStub,
            $userRetrieverStub,
            $messageSenderStub,
            $entityManagerStub,
            $observeeRetrieverStub
        );

        $birthdayAddCommand->execute();
    }
}
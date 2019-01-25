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
    public function validationViolationsProvider()
    {
        return [
          ['Add 532464241 32.09.2000', '2000-09-32', ['info'], "Обнаружены ошибки:\n\nДата неправильная. Она должна быть в формате DD.MM.YYYY. Например: 13.10.1996.\n"],
          ['Add fjwekwlelrfrkgkrelreti 30.09.2000', '2000-09-30', ['error' => true], "Обнаружены ошибки:\n\nЮзер с таким id не найден в VK.\n"],
          ['Add fjwekwlelrfrkgkrelreti 32.09.2000', '2000-09-32', ['error' => true], "Обнаружены ошибки:\n\nЮзер с таким id не найден в VK.\nДата неправильная. Она должна быть в формате DD.MM.YYYY. Например: 13.10.1996.\n"]
        ];
    }

    /**
     * @test
     * @dataProvider validationViolationsProvider
     *
     * @param $receivedMessage
     * @param $birthday
     * @param $vkUserObject
     * @param $messageToSend
     */
    public function correctlyRespondsToMessageValidationViolations(
        $receivedMessage,
        $birthday,
        $vkUserObject,
        $messageToSend
    ) {
        $msgStub = new \stdClass();
        $msgStub->from_id = 5;
        $msgStub->text = $receivedMessage;

        /** @var UserRetriever|MockObject $userRetrieverStub */
        $userRetrieverStub = $this->createMock(UserRetriever::class);

        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
            ->method('send')
            ->with($messageToSend, $msgStub->from_id);
        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);

        /** @var ObserveeDataRetriever|MockObject $observeeRetrieverStub */
        $observeeRetrieverStub = $this->createMock(ObserveeDataRetriever::class);
        $observeeRetrieverStub->method('getBirthdayFromMessage')
            ->willReturn($birthday);
        $observeeRetrieverStub->method('getVkUserObjectFromMessage')
            ->willReturn($vkUserObject);

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
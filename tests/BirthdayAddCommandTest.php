<?php

namespace VkBirthdayReminder\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VkBirthdayReminder\Commands\BirthdayAddCommand;
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\Helpers\ObserveeDataRetriever;
use VkBirthdayReminder\Helpers\UserRetriever;
use VkBirthdayReminder\Entities\{Observer, Observee};

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
        $observeeRetrieverStub->method('getBirthdayFromMessage')->willReturn($birthday);
        $observeeRetrieverStub->method('getVkUserObjectFromMessage')->willReturn($vkUserObject);

        $birthdayAddCommand = new BirthdayAddCommand(
            $msgStub,
            $userRetrieverStub,
            $messageSenderStub,
            $entityManagerStub,
            $observeeRetrieverStub
        );

        $birthdayAddCommand->execute();
    }

    /**
     * @test
     */
    public function correctlyHandlesNewObserver()
    {
        $msgStub = new \stdClass();
        $msgStub->from_id = 5;
        $msgStub->text = 'Add 98 18.10.1986';

        $vkObserveeData = [
            'response' => [
                0 => [
                    'id' => 98,
                    'first_name' => 'John',
                    'last_name' => 'Doe'
                ]
            ]
        ];
        $vkSenderData = [
            'response' => [
                0 => [
                    'id' => 37,
                    'first_name' => 'Jake',
                    'last_name' => 'Jones'
                ]
            ]
        ];

        /** @var ObserveeDataRetriever|MockObject $observeeRetrieverStub */
        $observeeRetrieverStub = $this->createMock(ObserveeDataRetriever::class);
        $observeeRetrieverStub->method('getBirthdayFromMessage')
                              ->willReturn('1986-10-18');
        $observeeRetrieverStub->method('getVkUserObjectFromMessage')->willReturn($vkObserveeData);

        $repositoryStub = $this->createMock(EntityRepository::class);
        $repositoryStub->method('findOneBy')->willReturn(null);

        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);
        $entityManagerStub->method('getRepository')->willReturn($repositoryStub);
        $entityManagerStub->expects($this->exactly(2))
                          ->method('persist')
                          ->withConsecutive(
                              [$this->isInstanceOf(Observer::class)],
                              [$this->isInstanceOf(Observee::class)]
                          );
        $entityManagerStub->expects($this->exactly(2))
                          ->method('flush');

        /** @var UserRetriever|MockObject $userRetrieverStub */
        $userRetrieverStub = $this->createMock(UserRetriever::class);
        $userRetrieverStub->method('getUser')->willReturn($vkSenderData);

        $observeeVkId = $vkObserveeData['response'][0]['id'];
        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
            ->method('send')
            ->with("Теперь вы следите за днем рождения юзера с id {$observeeVkId}.", $msgStub->from_id);

        $birthdayAddCommand = new BirthdayAddCommand(
            $msgStub,
            $userRetrieverStub,
            $messageSenderStub,
            $entityManagerStub,
            $observeeRetrieverStub
        );

        $birthdayAddCommand->execute();
    }

    /**
     * @test
     */
    public function correctlyHandlesExistingObservee()
    {
        $msgStub = new \stdClass();
        $msgStub->from_id = 5;
        $msgStub->text = 'Add 98 18.10.1986';

        $vkObserveeData = [
            'response' => [
                0 => [
                    'id' => 98,
                    'first_name' => 'John',
                    'last_name' => 'Doe'
                ]
            ]
        ];

        /** @var UserRetriever|MockObject $userRetrieverStub */
        $userRetrieverStub = $this->createMock(UserRetriever::class);

        /** @var ObserveeDataRetriever|MockObject $observeeRetrieverStub */
        $observeeRetrieverStub = $this->createMock(ObserveeDataRetriever::class);
        $observeeRetrieverStub->method('getBirthdayFromMessage')
            ->willReturn('1986-10-18');
        $observeeRetrieverStub->method('getVkUserObjectFromMessage')->willReturn($vkObserveeData);

        $observeeVkId = $vkObserveeData['response'][0]['id'];
        $observeeStub = $this->createMock(Observee::class);
        $observeeStub->method('getVkId')
                     ->willReturn($observeeVkId);

        $observerStub = $this->createMock(Observer::class);
        $observerStub->method('getObservees')
                     ->willReturn(new ArrayCollection([$observeeStub]));

        $repositoryStub = $this->createMock(EntityRepository::class);
        $repositoryStub->method('findOneBy')->willReturn($observerStub);

        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);
        $entityManagerStub->method('getRepository')->willReturn($repositoryStub);

        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
            ->method('send')
            ->with('Вы уже следите за днем рождения этого юзера', $msgStub->from_id);

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
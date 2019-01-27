<?php
/**
 * Created by PhpStorm.
 * User: bocmah
 * Date: 26/01/2019
 * Time: 14:42
 */

namespace VkBirthdayReminder\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VkBirthdayReminder\Commands\UpdateCommand;
use VkBirthdayReminder\Entities\Observer;
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\Helpers\ObserveeDataRetriever;
use VkBirthdayReminder\Helpers\UserRetriever;

class UpdateCommandTest extends TestCase
{
    /**
     * @test
     */
    public function informsObserverIfHeDoesNotExistInDatabase()
    {
        $msgStub = new \stdClass();
        $msgStub->from_id = 5;

        /** @var ObserveeDataRetriever|MockObject $observeeDataRetrieverStub */
        $observeeDataRetrieverStub = $this->createMock(ObserveeDataRetriever::class);

        $repositoryStub = $this->createMock(EntityRepository::class);
        $repositoryStub->method('findOneBy')->willReturn(null);

        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);
        $entityManagerStub->method('getRepository')->willReturn($repositoryStub);

        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
                          ->method('send')
                          ->with(
                              'Вы не найдены в базе. Вероятно вы ни разу не вызывали add команду.',
                              $msgStub->from_id
                          );

        $updateCommand = new UpdateCommand(
          $msgStub,
          $messageSenderStub,
          $entityManagerStub,
          $observeeDataRetrieverStub
        );

        $updateCommand->execute();
    }

    public function validationViolationsProvider()
    {
        return [
            ['Update 532464241 32.09.2000', '2000-09-32', ['info'], "Обнаружены ошибки:\n\nДата неправильная. Она должна быть в формате DD.MM.YYYY. Например: 13.10.1996.\n"],
            ['Update fjwekwlelrfrkgkrelreti 30.09.2000', '2000-09-30', ['error' => true], "Обнаружены ошибки:\n\nЮзер с таким id не найден в VK.\n"],
            ['Update fjwekwlelrfrkgkrelreti 32.09.2000', '2000-09-32', ['error' => true], "Обнаружены ошибки:\n\nЮзер с таким id не найден в VK.\nДата неправильная. Она должна быть в формате DD.MM.YYYY. Например: 13.10.1996.\n"]
        ];
    }

    /**
     * @test
     * @dataProvider validationViolationsProvider
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

        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
                          ->method('send')
                          ->with($messageToSend, $msgStub->from_id);

        $observerStub = $this->createMock(Observer::class);

        $repositoryStub = $this->createMock(EntityRepository::class);
        $repositoryStub->method('findOneBy')->willReturn($observerStub);

        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);
        $entityManagerStub->method('getRepository')->willReturn($repositoryStub);

        /** @var ObserveeDataRetriever|MockObject $observeeRetrieverStub */
        $observeeRetrieverStub = $this->createMock(ObserveeDataRetriever::class);
        $observeeRetrieverStub->method('getBirthdayFromMessage')->willReturn($birthday);
        $observeeRetrieverStub->method('getVkUserObjectFromMessage')->willReturn($vkUserObject);

        $updateCommand = new UpdateCommand(
            $msgStub,
            $messageSenderStub,
            $entityManagerStub,
            $observeeRetrieverStub
        );

        $updateCommand->execute();
    }

    /**
     * @test
     */
    public function correctlyRespondsToNotFoundObservee()
    {
        $msgStub = new \stdClass();
        $msgStub->from_id = 5;
        $msgStub->text = 'Update 12345 18.05.1994';

        $vkObserveeData = [
            'response' => [
                0 => [
                    'id' => 98,
                    'first_name' => 'John',
                    'last_name' => 'Doe'
                ]
            ]
        ];

        /** @var ObserveeDataRetriever|MockObject $observeeRetrieverStub */
        $observeeRetrieverStub = $this->createMock(ObserveeDataRetriever::class);
        $observeeRetrieverStub->method('getBirthdayFromMessage')->willReturn('1994-05-18');
        $observeeRetrieverStub->method('getVkUserObjectFromMessage')->willReturn($vkObserveeData);

        $observerStub = $this->createMock(Observer::class);

        $repositoryStub = $this->createMock(EntityRepository::class);
        $repositoryStub->method('findOneBy')
                       ->will($this->onConsecutiveCalls($observerStub, null));

        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);
        $entityManagerStub->method('getRepository')->willReturn($repositoryStub);

        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
                          ->method('send')
                          ->with(
                              'Вы не следите за пользователем с этим id. Для начала добавьте его в список отслеживаемых с помощью команды add.',
                              $msgStub->from_id
                          );

        $updateCommand = new UpdateCommand(
            $msgStub,
            $messageSenderStub,
            $entityManagerStub,
            $observeeRetrieverStub
        );

        $updateCommand->execute();
    }
}
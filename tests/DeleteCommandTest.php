<?php

namespace VkBirthdayReminder\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VkBirthdayReminder\Commands\DeleteCommand;
use VkBirthdayReminder\Entities\Observer;
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\Helpers\ObserveeDataRetriever;

class DeleteCommandTest extends TestCase
{
    /**
     * @test
     */
    public function correctlyRespondsToNotFoundObserver()
    {
        $msgStub = new \stdClass();
        $msgStub->from_id = 5;
        $msgStub->text = 'Delete 12345';

        $repositoryStub = $this->createMock(EntityRepository::class);
        $repositoryStub->method('findOneBy')->willReturn(null);

        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);
        $entityManagerStub->method('getRepository')->willReturn($repositoryStub);

        /** @var ObserveeDataRetriever|MockObject $observeeRetrieverStub */
        $observeeRetrieverStub = $this->createMock(ObserveeDataRetriever::class);

        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
            ->method('send')
            ->with(
                'Вы не найдены в базе. Вероятно вы ни разу не вызывали add команду.',
                $msgStub->from_id
            );

        $deleteCommand = new DeleteCommand(
          $msgStub,
          $messageSenderStub,
          $entityManagerStub,
          $observeeRetrieverStub
        );

        $deleteCommand->execute();
    }

    /**
     * @test
     */
    public function correctlyRespondsToNoUserValidationViolation()
    {
        $msgStub = new \stdClass();
        $msgStub->from_id = 5;
        $msgStub->text = 'Delete 12345';

        $vkObserveeData = [
            'error' => true
        ];

        $observerStub = $this->createMock(Observer::class);

        $repositoryStub = $this->createMock(EntityRepository::class);
        $repositoryStub->method('findOneBy')->willReturn($observerStub);

        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);
        $entityManagerStub->method('getRepository')->willReturn($repositoryStub);

        /** @var ObserveeDataRetriever|MockObject $observeeRetrieverStub */
        $observeeRetrieverStub = $this->createMock(ObserveeDataRetriever::class);
        $observeeRetrieverStub->method('getVkUserObjectFromMessage')->willReturn($vkObserveeData);

        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
            ->method('send')
            ->with(
                "Обнаружены ошибки:\n\nЮзер с таким id не найден в VK.\n",
                $msgStub->from_id
            );

        $deleteCommand = new DeleteCommand(
            $msgStub,
            $messageSenderStub,
            $entityManagerStub,
            $observeeRetrieverStub
        );

        $deleteCommand->execute();
    }
}
<?php

namespace VkBirthdayReminder\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VkBirthdayReminder\Entities\Observer;
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\Commands\NotifyCommand;

class NotifyCommandTest extends TestCase
{
    public function notifyToggleProvider()
    {
        return [
          [false, 'Теперь бот будет присылать вам уведомления, даже если ни у кого из ваших отслеживаемых людей нет ближайших дней рождений.'],
          [true, 'Теперь бот будет присылать вам уведомления, только если у кого-то из ваших друзей скоро день рождения.']
        ];
    }

    /**
     * @test
     * @dataProvider notifyToggleProvider
     *
     * @param $originalNotifiableState
     * @param $message
     */
    public function togglesNotifyState($originalNotifiableState, $message)
    {
        $senderId = 1;
        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
            ->method('send')
            ->with(
                $message,
                $senderId
            );

        $observerStub = $this->createMock(Observer::class);
        $observerStub->method('getIsNotifiable')->willReturn($originalNotifiableState);
        $observerStub->expects($this->once())
                     ->method('setIsNotifiable')
                     ->with(!$originalNotifiableState);

        $repositoryStub = $this->createMock(EntityRepository::class);
        $repositoryStub->method('findOneBy')->willReturn($observerStub);

        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);
        $entityManagerStub->method('getRepository')->willReturn($repositoryStub);
        $entityManagerStub->expects($this->once())
                          ->method('flush');

        $notifyCommand = new NotifyCommand(
          $senderId,
          $messageSenderStub,
          $entityManagerStub
        );

        $notifyCommand->execute();
    }

    /**
     * @test
     */
    public function informsObserverIfHeDoesNotExistInDatabase()
    {
        $senderId = 1;

        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
                          ->method('send')
                          ->with(
                                  'Вы не найдены в базе. Вероятно вы ни разу не вызывали add команду.',
                                  $senderId
                          );
        $repositoryStub = $this->createMock(EntityRepository::class);
        $repositoryStub->method('findOneBy')->willReturn(null);


        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);
        $entityManagerStub->method('getRepository')->willReturn($repositoryStub);

        $notifyCommand = new NotifyCommand(
            $senderId,
            $messageSenderStub,
            $entityManagerStub
        );

        $notifyCommand->execute();
    }
}
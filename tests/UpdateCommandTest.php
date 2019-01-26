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
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\Helpers\ObserveeDataRetriever;

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
}
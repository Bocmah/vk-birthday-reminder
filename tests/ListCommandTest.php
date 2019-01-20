<?php

namespace VkBirthdayReminder\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VkBirthdayReminder\Commands\ListCommand;
use VkBirthdayReminder\Entities\{Observer, Observee};
use VkBirthdayReminder\Helpers\MessageSender;

class ListCommandTest extends TestCase
{
    /**
     * @test
     */
    public function informsObserverIfHeDoesNotExistInDatabase()
    {
        $observerVkId = 1;

        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
            ->method('send')
            ->with(
                'Вы не найдены в базе. Вероятно вы ни разу не вызывали add команду.',
                $observerVkId
            );
        $repositoryStub = $this->createMock(EntityRepository::class);
        $repositoryStub->method('findOneBy')->willReturn(null);


        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);
        $entityManagerStub->method('getRepository')->willReturn($repositoryStub);

        $notifyCommand = new ListCommand(
            $observerVkId,
            $messageSenderStub,
            $entityManagerStub
        );

        $notifyCommand->execute();
    }

    public function listsObserveesProvider()
    {
        return [
          [
              [
                  [
                      'date' => new \DateTime('1996-10-13'),
                      'vk_id' => 1,
                      'first_name' => 'Ivan',
                      'last_name' => 'Petrov'
                  ],
                  [
                      'date' => new \DateTime('1993-12-25'),
                      'vk_id' => 2,
                      'first_name' => 'Vasilisa',
                      'last_name' => 'Ivanova'
                  ]
              ],
              "*id1 (Ivan Petrov) - 13.10.1996\n*id2 (Vasilisa Ivanova) - 25.12.1993\n"
          ],
          [
              [],
              'Вы еще не отслеживаете ДР ни одного юзера.'
          ]
        ];
    }

    /**
     * @dataProvider listsObserveesProvider
     * @test
     * @param array $observeeData
     * @param string $message
     */
    public function listsObservees(array $observeeData, string $message)
    {
        $observerVkId = 3;
        $observerStub = $this->createMock(Observer::class);
        $observerStub->method('getObservees')->willReturn($this->createStubObservees($observeeData));

        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
            ->method('send')
            ->with(
                $message,
                $observerVkId
            );
        $repositoryStub = $this->createMock(EntityRepository::class);
        $repositoryStub->method('findOneBy')->willReturn($observerStub);


        /** @var EntityManager|MockObject $entityManagerStub */
        $entityManagerStub = $this->createMock(EntityManager::class);
        $entityManagerStub->method('getRepository')->willReturn($repositoryStub);

        $notifyCommand = new ListCommand(
          $observerVkId,
          $messageSenderStub,
          $entityManagerStub
        );

        $notifyCommand->execute();
    }

    /**
     * @param array $observeeData
     * @return ArrayCollection
     */
    protected function createStubObservees(array $observeeData)
    {
        $collection = new ArrayCollection();

        foreach ($observeeData as $observeeDataUnit) {
            $observee = $this->createMock(Observee::class);
            $observee->method('getBirthday')->willReturn($observeeDataUnit['date']);
            $observee->method('getVkId')->willReturn($observeeDataUnit['vk_id']);
            $observee->method('getFirstName')->willReturn($observeeDataUnit['first_name']);
            $observee->method('getLastName')->willReturn($observeeDataUnit['last_name']);

            $collection->add($observee);
        }

        return $collection;
    }
}
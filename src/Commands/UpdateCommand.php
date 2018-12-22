<?php

namespace VkBirthdayReminder\Commands;

use Doctrine\ORM\EntityManager;
use VkBirthdayReminder\Helpers\{MessageSender, ObserveeDataRetriever};
use VkBirthdayReminder\Validator\Constraints as CustomConstraints;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use VkBirthdayReminder\Traits\ErrorMessageTrait;

class UpdateCommand implements CommandInterface
{
    use ErrorMessageTrait;

    /**
     * VK message object
     */
    protected $msg;

    /**
     * @var MessageSender
     */
    protected $messageSender;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ObserveeDataRetriever
     */
    protected $observeeDataRetriever;

    public function __construct(
        $msg,
        MessageSender $messageSender,
        EntityManager $entityManager,
        ObserveeDataRetriever $observeeDataRetriever
    ) {
        $this->msg = $msg;
        $this->messageSender = $messageSender;
        $this->entityManager = $entityManager;
        $this->observeeDataRetriever = $observeeDataRetriever;
    }

    public function execute()
    {
        $senderId = $this->msg->from_id;
        $observer = $this->getObserverIfExists($senderId);

        if (!$observer) {
            return $this->messageSender->send(
                'Вы не найдены в базе. Вероятно вы ни разу не вызывали add команду.',
                $senderId
            );
        }

        $observeeData = [
            'date_of_birth' => $this->observeeDataRetriever->getBirthdayFromMessage($this->msg->text),
            'user' => $this->observeeDataRetriever->getVkUserObjectFromMessage($this->msg->text)
        ];

        $violations = $this->performValidation($observeeData);

        if (count($violations) !== 0) {
            $errorMessage = $this->composeErrorMessage($violations);

            return $this->messageSender->send($errorMessage, $senderId);
        }

        $observeeData['user'] = $observeeData['user']['response'][0];
        $observeeVkId = $observeeData['user']['id'];

        $observee = $this->getObserveeIfExists($observer->getId(),$observeeVkId);

        if (!$observee) {
            return $this->messageSender->send(
                'Вы не следите за пользователем с этим id. 
                     Для начала добавьте его в список отслеживаемых с помощью команды add.',
                $senderId
            );
        }

        $observee->setBirthday(new \DateTime($observeeData['date_of_birth']));
        $this->entityManager->flush();

        return $this->messageSender->send(
            "День рождения юзера с id {$observeeVkId} успешно обновлен.",
            $senderId
        );
    }

    /**
     * Return an observer object if $senderId already exists in the observers table.
     * Null otherwise.
     *
     * @param int $senderId
     * @return null|object
     */
    protected function getObserverIfExists(int $senderId)
    {
        return $this->entityManager->getRepository('VkBirthdayReminder\Entities\Observer')->findOneBy([
                'vkId' => $senderId
        ]);
    }

    /**
     * Return an observee object if it exists in the observer's list.
     * Null otherwise.
     *
     * @param int $observerId
     * @param int $observeeVkId
     * @return null|object
     */
    protected function getObserveeIfExists(int $observerId, $observeeVkId)
    {
        return $this->entityManager->getRepository('VkBirthdayReminder\Entities\Observee')->findOneBy([
                'observer' => $observerId,
                'vkId' => $observeeVkId
        ]);
    }

    /**
     * Validates observee data and returns an array of errors.
     *
     * @param array $observeeData
     * @return ConstraintViolationListInterface
     */
    protected function performValidation(array $observeeData): ConstraintViolationListInterface
    {
        $validator = Validation::createValidator();
        $constraint = new Constraints\Collection([
            'user' => new CustomConstraints\NoUserError(),
            'date_of_birth' => new Constraints\Date([
                'message' => 'Дата неправильная. Она должна быть в формате DD.MM.YYYY. Например: 13.10.1996.'
            ])
        ]);

        return $validator->validate($observeeData, $constraint);
    }
}
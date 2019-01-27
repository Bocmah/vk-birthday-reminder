<?php

namespace VkBirthdayReminder\Commands;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use VkBirthdayReminder\Traits\ErrorMessageTrait;
use VkBirthdayReminder\Validator\Constraints as CustomConstraints;
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\Helpers\ObserveeDataRetriever;

/**
 * Encapsulates logic related to deleting an existing observee.
 */
class DeleteCommand implements CommandInterface
{
    use ErrorMessageTrait;

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

        $vkUser = $this->observeeDataRetriever->getVkUserObjectFromMessage($this->msg->text);

        $violations = $this->performValidation($vkUser);

        if (count($violations) !== 0) {
            $errorMessage = $this->composeErrorMessage($violations);

            return $this->messageSender->send($errorMessage, $senderId);
        }

        $vkUser = $vkUser['response'][0];

        $observee = $this->getObserveeIfExists($observer->getId(), $vkUser['id']);

        if (!$observee) {
            return $this->messageSender->send(
                'Вы не следите за пользователем с этим id. Для начала добавьте его в список отслеживаемых с помощью команды add.',
                $senderId
            );
        }

        $this->entityManager->remove($observee);
        $this->entityManager->flush();

        return $this->messageSender->send(
            "Юзер с id {$vkUser['id']} был успешно удален из списка отслеживаемых",
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
     * @param array $vkUser
     * @return ConstraintViolationListInterface
     */
    protected function performValidation(array $vkUser): ConstraintViolationListInterface
    {
        $validator = Validation::createValidator();
        $userConstraint = new CustomConstraints\NoUserError();

        return $validator->validate($vkUser, $userConstraint);
    }
}
<?php

namespace VkBirthdayReminder\Commands;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use VkBirthdayReminder\Helpers\{UserRetriever, MessageSender};
use Symfony\Component\Validator\Validation;
use VkBirthdayReminder\Validator\Constraints as CustomConstraints;
use Symfony\Component\Validator\Constraints;
use VkBirthdayReminder\Entities\{Observer, Observee};
use VkBirthdayReminder\Traits\ErrorMessageTrait;

/**
 * Encapsulates logic related to adding a new Observer-Observee relationship.
 * Observers 'watch' for observees' birthdays.
 */
class BirthdayAddCommand implements CommandInterface
{
    use ErrorMessageTrait;

    /**
     * VK message object
     */
    protected $msg;

    /**
     * @var UserRetriever
     */
    protected $userRetriever;

    /**
     * @var MessageSender
     */
    protected $messageSender;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * BirthdayAddCommand constructor.
     * @param $msg
     * @param UserRetriever $userRetriever
     * @param MessageSender $messageSender
     * @param EntityManager $entityManager
     */
    public function __construct(
        $msg,
        UserRetriever $userRetriever,
        MessageSender $messageSender,
        EntityManager $entityManager
    ) {
        $this->msg = $msg;
        $this->userRetriever = $userRetriever;
        $this->messageSender = $messageSender;
        $this->entityManager = $entityManager;
    }

    /**
     * Execute the command.
     */
    public function execute()
    {
        $senderId = $this->msg->from_id;
        $observeeData = $this->getObserveeData();

        $violations = $this->performValidation($observeeData);

        if (count($violations) !== 0) {
            $errorMessage = $this->composeErrorMessage($violations);

            return $this->messageSender->send($errorMessage, $senderId);
        }

        $observeeData['user'] = $observeeData['user']['response'][0];
        $isNewObserver = false;
        $observer = $this->getObserverIfExists($senderId);

        if (!$observer) {
            $isNewObserver = true;
            $senderVk = $this->userRetriever->getUser($senderId, true)['response'][0];

            $observer = $this->storeObserver($senderVk);
        }

        $observeeVkId = $observeeData['user']['id'];

        if (!$isNewObserver) {
            $observeeExists = $observer->getObservees()->exists(function ($key, $observee) use ($observeeVkId) {
               return $observee->getVkId() === $observeeVkId;
            });

            if ($observeeExists) {
                return $this->messageSender->send('Вы уже следите за днем рождения этого юзера', $senderId);
            }
        }

        $this->storeObservee(
            $observeeData,
            $observer
        );

        return $this->messageSender->send(
            "Теперь вы следите за днем рождения юзера с id {$observeeVkId}.",
            $senderId
        );
    }

    /**
     * Get observee data from the message.
     *
     * @return array
     */
    protected function getObserveeData(): array
    {
        $observeeData = [];
        $messageParts = explode(" ", $this->msg->text);
        $userId = $messageParts[1];
        [$day, $month, $year] = explode(".",$messageParts[2]);
        $observeeData['date_of_birth'] = "{$year}-{$month}-{$day}";
        $observeeData['user'] = $this->userRetriever->getUser($userId, true);

        return $observeeData;
    }

    /**
     * Validate an array of data from the command.
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

    /**
     * Return an observer object if $senderId already exists in the observers table.
     * Null otherwise.
     *
     * @param int $senderId
     * @return null|object
     */
    protected function getObserverIfExists(int $senderId)
    {
        return $this->entityManager->getRepository('VkBirthdayReminder\Entities\Observer')->findOneBy(
            [
                'vkId' => $senderId
            ]
        );
    }

    /**
     * Store the new observer in the database.
     *
     * @param array $senderVk Info about the request initiator (aka message sender).
     * @return Observer
     */
    protected function storeObserver(array $senderVk): Observer
    {
        $observer = new Observer();

        $observer->setVkId($senderVk['id']);
        $observer->setFirstName($senderVk['first_name']);
        $observer->setLastName($senderVk['last_name']);

        $this->entityManager->persist($observer);
        $this->entityManager->flush();

        return $observer;
    }

    /**
     * Store the new observee in the database.
     *
     * @param array $observeeData
     * @param Observer $observer
     * @return Observee
     */
    protected function storeObservee(array $observeeData, Observer $observer): Observee
    {
        $observee = new Observee();

        $observee->setVkId($observeeData['user']['id']);
        $observee->setFirstName($observeeData['user']['first_name']);
        $observee->setLastName($observeeData['user']['last_name']);
        $observee->setBirthday(new \DateTime($observeeData['date_of_birth']));
        $observee->setObserver($observer);

        $this->entityManager->persist($observee);
        $this->entityManager->flush();

        return $observee;
    }
}
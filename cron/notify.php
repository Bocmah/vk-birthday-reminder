<?php

use VkBirthdayReminder\Entities\{Observee, Observer};

require_once __DIR__.'/../bootstrap.php';

$entityManager = $container->getParameter('entity_manager');
$messageSender = $container->get('msg_sender');

$observers = $entityManager->getRepository('VkBirthdayReminder\Entities\Observer')->findAll();

$today = new DateTime('today', new DateTimeZone('Europe/Moscow'));
$tomorrow = new DateTime('tomorrow', new DateTimeZone('Europe/Moscow'));
$template = "*id%d (%s %s)\n";

foreach ($observers as $observer) {
    $message = '';
    $observeesWhoHaveBirthdayToday = '';
    $observeesWhoHaveBirthdayTomorrow = '';
    /** @var Observer $observer */
    $observees = $observer->getObservees();

    $observees->map(function (Observee $observee) use (
        $today,
        $tomorrow,
        &$observeesWhoHaveBirthdayToday,
        &$observeesWhoHaveBirthdayTomorrow,
        $template
    ) {
       /** @var \DateTime $birthday */
       $birthday = $observee->getBirthday();
       $birthday->setTimezone(new DateTimeZone('Europe/Moscow'));

       if ($birthday->format('d-m') == $today->format('d-m')) {
           $observeesWhoHaveBirthdayToday .= sprintf(
               $template,
               $observee->getVkId(),
               $observee->getFirstName(),
               $observee->getLastName()
           );
       } elseif ($birthday->format('d-m') == $tomorrow->format('d-m')) {
           $observeesWhoHaveBirthdayTomorrow .= sprintf(
               $template,
               $observee->getVkId(),
               $observee->getFirstName(),
               $observee->getLastName()
           );
       }
    });

    if ($observeesWhoHaveBirthdayToday) {
        $message .= "Сегодня дни рождения у этих людей:\n\n" . $observeesWhoHaveBirthdayToday . "\n\n";
    } elseif ($observeesWhoHaveBirthdayTomorrow) {
        $message .= "Завтра дни рождения у этих людей:\n\n" . $observeesWhoHaveBirthdayTomorrow;
    } else {
        if (!$observer->getIsNotifiable()) {
            continue;
        }

        $message .= 'Сегодня и завтра дней рождения не предвидится.';
    }

    $messageSender->send($message, $observer->getVkId());
}


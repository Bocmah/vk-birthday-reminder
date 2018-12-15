<?php

use VkBirthdayReminder\Entities\Observee;

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
    $observees = $observer->getObservees();

    $observees->map(function (Observee $observee) use (
        $today,
        $tomorrow,
        &$observeesWhoHaveBirthdayToday,
        &$observeesWhoHaveBirthdayTomorrow,
        $template
    ) {
       $birthday = new DateTime($observee->getBirthday(), new DateTimeZone('Europe/Moscow'));

       if ($birthday == $today) {
           $observeesWhoHaveBirthdayToday .= sprintf(
               $template,
               $observee->getVkId(),
               $observee->getFirstName(),
               $observee->getLastName()
           );
       } elseif ($birthday == $tomorrow) {
           $observeesWhoHaveBirthdayTomorrow .= sprintf(
               $template,
               $observee->getVkId(),
               $observee->getFirstName(),
               $observee->getLastName()
           );
       }
    });

    if ($observeesWhoHaveBirthdayToday) {
        $message .= "Сегодня день рождения у этих людей:\n\n" . $observeesWhoHaveBirthdayToday . "\n\n";
    } elseif ($observeesWhoHaveBirthdayTomorrow) {
        $message .= "Завтра день рождения у этих людей:\n\n" . $observeesWhoHaveBirthdayTomorrow;
    } else {
        $message .= 'Сегодня и завтра дней рождений не предвидится.';
    }

    $messageSender->send($message, $observer->getVkId());
}


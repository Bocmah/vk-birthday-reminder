<?php

require_once __DIR__.'/../bootstrap.php';

$entityManager = $container->getParameter('entity_manager');
$messageSender = $container->get('msg_sender');

$observers = $entityManager->getRepository('VkBirthdayReminder\Entities\Observer')->findAll();

foreach ($observers as $observer) {
    $message = "Список юзеров, за которыми вы следите:\n\n";
    $observees = $observer->getObservees();

    $observees->map(function ($observee) use (&$message) {
       $observeeFullName = $observee->getFirstName() . ' ' . $observee->getLastName();
       $message .= $observeeFullName . "\n";
    });

    $messageSender->send($message, $observer->getVkId());
}
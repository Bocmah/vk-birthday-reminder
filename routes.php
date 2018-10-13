<?php

use Symfony\Component\Routing\{RouteCollection, Route};

$routes = new RouteCollection();

$routes->add("message_store", new Route("/bot", array(
    "_controller" => "VkBirthdayReminder\Controllers\MessageController::store"
), array(), array(), "", array(), array("POST")));

return $routes;
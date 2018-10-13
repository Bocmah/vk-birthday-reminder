<?php

use Symfony\Component\Routing\{RouteCollection, Route};

$routes = new RouteCollection();

$routes->add("profile_update", new Route("/profile/edit", array(
    "_controller" => "StudentList\Controllers\ProfileController::update"
), array(), array(), "", array(), array("POST")));

return $routes;
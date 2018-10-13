<?php

use Symfony\Component\HttpFoundation\Request;
use VkBirthdayReminder\Database\Connection;

require_once __DIR__."/../vendor/autoload.php";
$container = require_once __DIR__."/../container.php";
$config = require_once __DIR__."/../dbconfig.php";

$connection = Connection::make($config);

$container->setParameter("routes", require_once __DIR__."/../routes.php");
$container->setParameter("container", $container);
$container->setParameter("connection", $connection);

$request = Request::createFromGlobals();

$response = $container->get("app")->handle($request);

$response->send();
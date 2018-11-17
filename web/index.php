<?php

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__."/../bootstrap.php";

$request = Request::createFromGlobals();

$response = $container->get("app")->handle($request);

$response->send();
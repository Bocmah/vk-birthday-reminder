<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once __DIR__."/vendor/autoload.php";
$container = require_once __DIR__."/container.php";
$connection = require_once __DIR__."/dbconfig.php";
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src/Entities"), true);

$container->setParameter("routes", require_once __DIR__."/routes.php");
$container->setParameter("container", $container);
$container->setParameter('entity_manager', EntityManager::create($connection, $config));
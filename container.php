<?php

use Symfony\Component\DependencyInjection\{ContainerBuilder, Reference};
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpKernel\Controller\{ArgumentResolver, ContainerControllerResolver};
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use VkBirthdayReminder\App;

$containerBuilder = new ContainerBuilder();
// Symfony components
$containerBuilder->register("context", RequestContext::class);
$containerBuilder->register("matcher", UrlMatcher::class)
    ->setArguments(array("%routes%", new Reference("context")));
$containerBuilder->register("request_stack", RequestStack::class);
$containerBuilder->register("controller_resolver", ContainerControllerResolver::class)
    ->setArguments(array("%container%"));
$containerBuilder->register("argument_resolver", ArgumentResolver::class);

// Listeners
$containerBuilder->register("listener.router", RouterListener::class)
    ->setArguments(array(new Reference("matcher"), new Reference("request_stack")));

// Event dispatcher
$containerBuilder->register("dispatcher", EventDispatcher::class)
    ->addMethodCall("addSubscriber", array(new Reference("listener.router")));

// App
$containerBuilder->register("app", App::class)->setArguments(
    array(
        new Reference("dispatcher"),
        new Reference("controller_resolver"),
        new Reference("request_stack"),
        new Reference("argument_resolver")
    )
);

return $containerBuilder;
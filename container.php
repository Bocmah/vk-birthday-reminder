<?php

use Symfony\Component\DependencyInjection\{ContainerBuilder, Reference};
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpKernel\Controller\{ArgumentResolver, ContainerControllerResolver};
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use VkBirthdayReminder\EventListeners\ConfirmationKeyListener;
use VkBirthdayReminder\App;
use VkBirthdayReminder\Controllers\MessageController;
use VkBirthdayReminder\Helpers\{MessageSender, UserRetriever, CommandParser, ObserveeDataRetriever};
use VkBirthdayReminder\Handlers\MessageHandler;
use VkBirthdayReminder\Commands\CommandFactory;

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
$containerBuilder->register("listener.confirmation_key", ConfirmationKeyListener::class);

// Event dispatcher
$containerBuilder->register("dispatcher", EventDispatcher::class)
    ->addMethodCall("addSubscriber", array(new Reference("listener.router")))
    ->addMethodCall("addSubscriber", array(new Reference("listener.confirmation_key")));

// App
$containerBuilder->register("app", App::class)->setArguments(
    array(
        new Reference("dispatcher"),
        new Reference("controller_resolver"),
        new Reference("request_stack"),
        new Reference("argument_resolver")
    )
);

// Helpers
$containerBuilder->register("msg_sender", MessageSender::class);
$containerBuilder->register("usr_retriever", UserRetriever::class);
$containerBuilder->register("command_parser", CommandParser::class);
$containerBuilder->register('observee_data_retriever', ObserveeDataRetriever::class)
    ->setArguments(array(
        new Reference('usr_retriever')
    ));

// Commands
$containerBuilder->register("command_factory", CommandFactory::class);

// Handlers
$containerBuilder->register("msg_handler", MessageHandler::class)
    ->setArguments(
        array(
            new Reference("usr_retriever"),
            new Reference("msg_sender"),
            new Reference("command_parser"),
            new Reference("command_factory"),
            '%entity_manager%',
            new Reference('observee_data_retriever')
        )
    );


// Controllers
$containerBuilder->register("VkBirthdayReminder\Controllers\MessageController", MessageController::class)
    ->setArguments(
        array(
            new Reference("msg_handler")
        )
    );

return $containerBuilder;
<?php

@include_once __DIR__ . '/vendor/autoload.php';

$routes = include __DIR__.'/config/routes.php';

$options = [
	//
];

Kirby::plugin('genxbe/k3x-cli-tools', [
    'routes' => $routes,
	'commands' => array_merge(
		X\Commands\PluginCommands\ListCommand::render(),
		X\Commands\PluginCommands\DeleteCommand::render(),
	),
]);

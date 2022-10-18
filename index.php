<?php

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('genxbe/k3x-cli-tools', [
	'options' => [
		'maintenance' => true,
	],

	'snippets' => [
		'maintenance' => __DIR__ . '/snippets/maintenance.php',
	],

	'commands' => array_merge(
		/** Kirby Commands **/
		X\Commands\KirbyCommands\RootsCommand::render(),
		X\Commands\KirbyCommands\DownCommand::render(),
		X\Commands\KirbyCommands\UpCommand::render(),

		/** Plugin Commands **/
		X\Commands\PluginCommands\ListCommand::render(),
		X\Commands\PluginCommands\DeleteCommand::render(),
		X\Commands\PluginCommands\PublishCommand::render(),
	),

	'hooks' => [
        'route:after' => function($route, $path, $method) {
			if (kirby()->option('genxbe.k3x-cli-tools.maintenance') === true) {
				$rootFolder = getcwd();
				$panelUrl = option('panel.slug') ?? 'panel';

				if(!kirby()->user() && Str::position(Url::current(),$panelUrl) === false && file_exists($rootFolder.'/.maintenance'))
				{
					$email = file_get_contents($rootFolder.'/.maintenance');
					snippet('maintenance', compact('email'));
					die();
				}
			}
        },
    ],
]);

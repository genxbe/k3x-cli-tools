<?php

namespace X\Commands\PluginCommands;

use Kirby\CLI\CLI;
use Kirby\Cms\Plugin;

use X\Support\H;
use X\Support\Plugins;
use X\Commands\Command;

class DeleteCommand extends Command
{
	public static string $commandName = 'x:plugins:delete';
	public static string $commandDescription = 'Delete a specific plugin (use with caution!)';
	public static array $commandArgs = [];

	public function __construct(CLI $cli)
	{
		$plugin = Plugins::select(
			title: 'Which plugin do you want to delete?',
			askForConfirmation: true,
		);

		$this->deletePlugin($plugin);
	}

	private function deletePlugin(Plugin $plugin): void
	{
		H::info('Deleting', $plugin->name().' (Please wait...)');

		try
		{
			H::cmd("composer remove {$plugin->name()}");
		}
		catch(\Exception $e)
		{
			H::debug($e->getMessage());
		}

		H::success('Finished', $plugin->name() .' has been deleted.');
	}
}

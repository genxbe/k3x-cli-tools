<?php

namespace X\Commands\PluginCommands;

use X\Cli;
use X\Commands\Command;

use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;

use function Termwind\{render};

class DeleteCommand extends Command
{
	public static string $commandName = 'x:plugins:delete';
	public static string $commandDescription = 'Delete a specific plugin (use with caution!)';
	public static array $commandArgs = [];

	private object $cli;

	public function __construct(object $cli)
	{
		$this->cli = $cli;

		$kirby = kirby();
		$cli = $this->cli;

		$sys = new \Kirby\Cms\System($kirby);

        $installedPlugins = [];
        $installedPluginsMenu = [];

        foreach($sys->plugins() as $plugin)
        {
            array_push($installedPlugins, $plugin);
            array_push($installedPluginsMenu, $plugin->name());
        }

        if(empty($installedPlugins))
        {
            $cli->error('There are no plugins installed.');
            die();
        }

		$itemCallable = function (CliMenu $menu) use ($cli, $installedPlugins) {
			$continue = $menu->cancellableConfirm('Are you sure?')->display('Yes!', 'Cancel');

			if ($continue) {
				$option = $menu->getSelectedItem()->getText();
				$option = array_keys($installedPlugins, $option)[0];

				if(is_null($option))
				{
					$cli->error('No plugin selected.');
					die();
				}

				$plugin = $installedPlugins[$option];

				Cli::info('Deleting', $plugin->name());
				Cli::success('Finished', $plugin->name() .' has been deleted.');
				die();
			} else {
				// Do nothing
			}
		};

		$menu = new CliMenuBuilder;
		$menu->setTitle('Select the plugin you want to delete');

		foreach($installedPlugins as $plugin)
		{
			$menu->addItem($plugin->name(), $itemCallable);
		}

		$menu->addLineBreak('-');
		$menu->setMarginAuto();

		$menu = $menu->build();
		$menu->open();


        // $this->task("Deleting {$plugin->name()}", function () use ($plugin)
        // {
        //     try
        //     {
        //         X::cmd("composer remove {$plugin->name()}");

        //         return true;
        //     }
        //     catch(\Exception $e)
        //     {
        //         X::debug($this, 'Error: '.$e->getMessage());
        //         return false;
        //     }
        // });
	}
}

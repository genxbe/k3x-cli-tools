<?php

namespace X\Support;

use Kirby\Cms\Plugin;
use X\Support\Menu\Menu;

class Plugins
{
	private static string $result;

	public static function get(): array
	{
		$sys = new \Kirby\Cms\System(kirby());

        $installedPlugins = [];

        foreach($sys->plugins() as $plugin)
        {
            array_push($installedPlugins, $plugin);
        }

        if(empty($installedPlugins))
        {
            H::error('ABORTED', 'There are no plugins installed.');
            die();
        }

		ksort($installedPlugins);

		return $installedPlugins;
	}

	public static function select($title = 'Select a plugin', bool $askForConfirmation = false): Plugin
    {
		$installedPlugins = self::get();

		$option = (new Menu(
			title: $title,
			options: $installedPlugins,
			askForConfirmation: $askForConfirmation,
		))->open();

		if(is_null($option))
		{
			H::error('ABORTED', 'No plugin selected or process aborted.');
			die();
		}

        return $installedPlugins[$option];
    }
}

<?php

namespace X\Commands\PluginCommands;

use X\Cli;
use X\Commands\Command;

use function Termwind\{render};

class ListCommand extends Command
{
	public static string $commandName = 'x:plugins:list';
	public static string $commandDescription = 'List all installed plugins.';
	public static array $commandArgs = [];

	private object $cli;

	public function __construct($cli)
	{
		$this->cli = $cli;

		$kirby = kirby();
		$sys = new \Kirby\Cms\System($kirby);

		$pluginsHtml = '';

        foreach($sys->plugins() as $plugin)
        {
            $pluginsHtml .= "<tr><td>{$plugin}</td><td>{$kirby->plugin($plugin)->description()}</td></tr>";
        }

        render(<<<HTML
            <table>
                <thead>
                    <tr>
                        <th>Plugin</th>
                        <th>Description</th>
                    </tr>
                </thead>
                {$pluginsHtml}
            </table>
        HTML);
	}
}

<?php

namespace X\Commands\PluginCommands;

use X\Support\H;
use X\Support\Plugins;
use X\Commands\Command;

use Kirby\CLI\CLI;
use Kirby\Toolkit\Str;

use function Termwind\{render};

class ListCommand extends Command
{
	public static string $commandName = 'x:plugins:list';
	public static string $commandDescription = 'List all installed plugins.';
	public static array $commandArgs = [];

	public function __construct(CLI $cli)
	{
		$plugins = Plugins::get();

		$pluginsHtml = '';

		foreach ($plugins as $plugin) {
			$p = kirby()->plugin($plugin);
			$pluginsHtml .= '
				<tr>
				<td>'. $plugin .'</td>
				<td>'. $p->version() .'</td>
				<td>'. Str::short($p->description(), 100) .'</td>
				</tr>
			';
		}

		render(<<<HTML
			<table>
				<thead>
					<tr>
						<th>Plugin</th>
						<th>Version</th>
						<th>Description</th>
					</tr>
				</thead>
				{$pluginsHtml}
			</table>
		HTML);
	}
}

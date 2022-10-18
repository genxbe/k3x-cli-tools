<?php

namespace X\Commands\KirbyCommands;

use X\Support\H;
use X\Commands\Command;

use Kirby\CLI\CLI;
use Kirby\Toolkit\Str;

use function Termwind\{render};

class RootsCommand extends Command
{
	public static string $commandName = 'x:kirby:roots';
	public static string $commandDescription = 'Pretty list of all configured roots.';
	public static array $commandArgs = [];

	public function __construct(CLI $cli)
	{
		$roots = $cli->roots();
		ksort($roots);

		$html = '';

		foreach ($roots as $key => $folder) {
			$html .= '
				<tr>
				<td>'. $key .'</td>
				<td>'. Str::replace($folder, getcwd(), '') .'</td>
				</tr>
			';
		}

		$cli->br();
		H::info('WORKING DIRECTORY', getcwd());
		$cli->br();

		render(<<<HTML
			<table>
				<thead>
					<tr>
						<th>Key</th>
						<th>Path</th>
					</tr>
				</thead>
				{$html}
			</table>
		HTML);
	}
}

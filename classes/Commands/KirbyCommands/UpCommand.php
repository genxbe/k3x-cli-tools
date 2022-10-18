<?php

namespace X\Commands\KirbyCommands;

use X\Support\H;
use X\Commands\Command;

use Kirby\CLI\CLI;

class UpCommand extends Command
{
	public static string $commandName = 'x:kirby:up';
	public static string $commandDescription = 'Removes the generic maintenance mode message.';
	public static array $commandArgs = [];

	public function __construct(CLI $cli)
	{
		try
		{
			unlink(getcwd().'/.maintenance');

			H::success('OK', 'Maintenance mode disabled.');
			die();
		}
		catch(\Exception $e)
		{
			X::debug($e->getMessage());
			die();
		}
	}
}

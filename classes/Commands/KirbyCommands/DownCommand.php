<?php

namespace X\Commands\KirbyCommands;

use X\Support\H;
use X\Commands\Command;

use Kirby\CLI\CLI;

class DownCommand extends Command
{
	public static string $commandName = 'x:kirby:down';
	public static string $commandDescription = 'Sets a generic maintenance mode message with an optional email address.';
	public static array $commandArgs = [
		'email' => [
			'description' => 'The email address for the maintenance mode (optional)',
			'required' => false
		],
	];

	public function __construct(CLI $cli)
	{
		try
		{
			$email = $cli->arg('email');

			if(empty($email))
			{
				file_put_contents(getcwd().'/.maintenance', '');
			}

			if(!empty($email))
			{
				file_put_contents(getcwd().'/.maintenance', $email);
			}

			H::error('WARNING', 'Maintenance mode enabled. Your site can only be accessed by logged in users.');
			die();
		}
		catch(\Exception $e)
		{
			X::debug($e->getMessage());
			die();
		}
	}
}

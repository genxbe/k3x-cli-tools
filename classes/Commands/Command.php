<?php

namespace X\Commands;

class Command
{
	private static string $commandName = 'x:command';
	private static string $commandDescription = 'This is a command';
	private static array $commandArgs = [];

	public static function render()
	{
		$class = static::class;

		return [
			static::$commandName => [
				'description' => static::$commandDescription,
				'args' => static::$commandArgs,
				'command' => fn($cli) => new $class($cli),
			],
		];
	}
}

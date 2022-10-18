<?php

namespace X\Support;

use function Termwind\{render};

/**
 * Helpers
 */
class H
{
	const VERBOSE = true;

	public static function cmd(string $cmd, string $folder = '')
    {
		if(empty($folder))
        {
            $folder = getcwd();
        }

        if(self::VERBOSE === false)
        {
            exec("cd {$folder} && {$cmd} 2>/dev/null >/dev/null &");
        }

        if(self::VERBOSE === true)
        {
            echo "cd {$folder} && {$cmd}".PHP_EOL;
            echo shell_exec("cd {$folder} && {$cmd}");
        }
    }

	public static function success($label, $msg)
    {
		$label = strtoupper($label);

        render(<<<HTML
            <div class="m-1">
                <div class="ml-2 px-1 bg-green-700">{$label}</div>
                <em class="ml-1">
                    {$msg}
                </em>
            </div>
        HTML);
    }

    public static function info($label, $msg)
    {
		$label = strtoupper($label);

        render(<<<HTML
            <div>
                <div class="ml-3 px-1 bg-blue-600">{$label}</div>
                <em class="ml-1">
                    {$msg}
                </em>
            </div>
        HTML);
    }

    public static function error($label, $msg)
    {
		$label = strtoupper($label);

        render(<<<HTML
            <div class="m-1">
                <div class="ml-2 px-1 bg-red-600">{$label}</div>
                <em class="ml-1">
                    {$msg}
                </em>
            </div>
        HTML);
    }

	public static function debug($msg)
	{
		if(self::VERBOSE === true)
		{
			self::error('DEBUG', $msg);
		}

		if(self::VERBOSE === false)
		{
			self::error('ERROR', 'Oops! Something went wrong, please try again.');
		}
	}
}

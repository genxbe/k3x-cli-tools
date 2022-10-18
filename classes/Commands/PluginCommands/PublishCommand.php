<?php

namespace X\Commands\PluginCommands;

use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\Action\ExitAction;
use PhpSchool\CliMenu\Action\GoBackAction;

use X\Support\H;
use X\Support\Plugins;
use X\Commands\Command;
use X\Support\Menu\Menu;
use X\Support\Autoloader;

use Kirby\CLI\CLI;
use Kirby\Cms\Plugin;
use Kirby\Filesystem\F;

class PublishCommand extends Command
{
	public static string $commandName = 'x:plugins:publish';
	public static string $commandDescription = 'Publish overwritable files found in the installed plugins. (Disclaimer: This plugin follows a certain folder scheme.)';
	public static array $commandArgs = [
		'type' => [
			'description' => 'The type of file you want to publish (blueprints, snippets, templates,...) Leave empty for list of possibilities.',
			'required' => false
		],
	];

	public const POSSIBLE_TYPES = [
		'blueprints',
		'snippets',
		'templates',
		'collections',
	];

	private string $type;
	private array $files;
	private Plugin $plugin;
	private array $selectedFiles = [];

	public function __construct(CLI $cli)
	{
		$type = $cli->arg('type');

		if(empty($type))
		{
			$type = $this->askFileType();
		}

		if(!in_array($type, static::POSSIBLE_TYPES))
		{
			H::error('ABORTED', 'No or faulty filetype selected or process aborted.');
			die();
		}

		$plugin = Plugins::select();
		$files = $this->getPluginFiles($type, $plugin);

		if(count($files) <= 0)
        {
            H::error('ABORTED', 'No '. $type .' files found for this plugin.');
            die();
        }

		$this->type = $type;
		$this->files = $files;
		$this->plugin = $plugin;

		$this->menu();
	}

	private function menu()
	{
		/**
		 * Add all or selected files
		 */
		$menuHandler = function (CliMenu $menu) {
            if(strpos($menu->getSelectedItem()->getText(), 'All '.$this->type) !== false)
            {
                $menu->close();
                $this->selectedFiles = $this->files;
            }
			else
			{
				if(in_array($menu->getSelectedItem()->getText(), $this->selectedFiles))
				{
					$this->selectedFiles = array_diff($this->selectedFiles, [$menu->getSelectedItem()->getText()]);
				}
				else
				{
					array_push($this->selectedFiles, $menu->getSelectedItem()->getText());
				}
			}
        };

		/**
		 * Cancel = don't do anything
		 */
        $deleteSelectedFiles = function(CliMenu $menu) {
            $this->selectedFiles = [];
            $menu->close();
        };

		/**
		 * Build main menu
		 */
		$menu = new CliMenuBuilder;
        $menu->setTitle('Select the '. $this->type .' you want to publish');
        $menu->addItem('All '. $this->type .' from this plugin', $menuHandler);
        $menu->addLineBreak('=');

		/**
		 * Build submenu
		 */
		$subMenu = $this->buildSubmenu($menuHandler);

        $parkedFiles = [];

        foreach($subMenu as $menuName => $menuValues)
        {
			/**
			 * If there's only 1 file in the submenu we will show it directly
			 */
            if(count($menuValues) <= 1)
            {
                $parkedFiles[] = $menuValues[0][0];
            }

			/**
			 * If there are more files we will add a submenu
			 */
            if(count($menuValues) > 1)
            {
                $menu->addSubMenu($menuName, function(CliMenuBuilder $b) use ($menuHandler, $menuName, $menuValues, &$selectedFiles, $deleteSelectedFiles) {
                    $b->setTitle($menuName)
                        ->addCheckboxItems($menuValues)
                        ->disableDefaultItems()
                        ->addLineBreak('-')
                        ->addItem('Go Back', new GoBackAction)
                        ->addItem('Confirm selection', new ExitAction)
                        ->addLineBreak('-')
                        ->addItem('Cancel', $deleteSelectedFiles);
                });
            }
        }

		$menu->addLineBreak('-');

		/**
		 * Add parked files
		 */
		foreach($parkedFiles as $file)
        {
            $menu->addCheckboxItem($file, $menuHandler);
        }

        $menu->addLineBreak('=');
        $menu->setMarginAuto();
        $menu->disableDefaultItems();
        $menu->addItem('Confirm selection', new ExitAction);
        $menu->addItem('Cancel', $deleteSelectedFiles);
        $menu = $menu->build();

		$menu->open();

		if(!empty($this->selectedFiles))
        {
            $this->copyFiles();

			H::success('HEY!', 'You just published some '. $this->type .'!');
        }
        else
        {
            H::error('ABORTED', 'No '. $this->type .' selected or process aborted.');
        }
	}

	private function copyFiles()
	{
		$kirby = kirby();
		$pluginPath = $this->plugin->root();
		$typeRoot = $kirby->root($this->type);

		foreach($this->selectedFiles as $file)
		{
			try
			{
				$fileBasePath = str_replace($pluginPath.'/'.$this->type, '', $file);
				H::info('COPYING', $fileBasePath);

				F::copy($pluginPath.'/'.$this->type.$fileBasePath, $typeRoot.$fileBasePath);
			}
			catch(\Exception $e)
			{
				H::debug($e);
			}
		}
	}

	private function buildSubMenu(callable $menuHandler)
	{
		$subs = [];

		$pluginPath = $this->plugin->root();

        foreach($this->files as $file => $key)
        {
            $subName = explode('/', $file)[0];
            $menuItem = str_replace($pluginPath.'/'.$this->type, '', $key);

            $subs[$subName][] = [$menuItem, $menuHandler];
        }

		return $subs;
	}

	private function getPluginFiles(string $type, Plugin $plugin): array
	{
        $pluginPath = $plugin->root();

        $autoloader = Autoloader::singleton(['dir' => $pluginPath]);

        $files = $autoloader->{$type}();
        ksort($files);

        return $files;
	}

	private function askFileType(): string
	{
		$possibleTypes = static::POSSIBLE_TYPES;

		$option = (new Menu(
			title: 'Which filetype do you want to publish?',
			options: $possibleTypes,
		))->open();

		if(is_null($option))
		{
			H::error('ABORTED', 'No or faulty filetype selected or process aborted.');
			die();
		}

		return $possibleTypes[$option];
	}
}

<?php

/**
 * File is based on "Laravel Console Menu".
 * https://github.com/nunomaduro/laravel-console-menu
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace X\Support\Menu;

use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;

class Menu extends CliMenuBuilder
{
    /**
     * The current option value.
     *
     * @var mixed
     */
    private $result;

	/**
	 * Confirm menu option?
	 *
	 * @var bool
	 */
	private $askForConfirmation;

    /**
     * Menu constructor.
     *
     * @param  string  $title
     * @param  array  $options
     */
    public function __construct($title = '', array $options = [], bool $askForConfirmation = false)
    {
		$this->askForConfirmation = $askForConfirmation;

		parent::__construct();

        $this->addLineBreak(' ')
            ->setTitleSeparator('-');

        $this->setMarginAuto();

        $this->setTitle($title);

        $this->addOptions($options);
    }

    /**
     * Adds a new option.
     *
     * @param  mixed  $value
     * @param  string  $label
     * @return \NunoMaduro\LaravelConsoleMenu\Menu
     */
    public function addOption($value, string $label): Menu
    {
        $this->addMenuItem(
            new MenuOption(
                $value,
                $label,
                function (CliMenu $menu) {
					$continue = true;

					if($this->askForConfirmation)
					{
						$continue = $menu->cancellableConfirm('Are you sure?')->display('Yes!', 'Cancel');
					}

					if($continue)
					{
						$this->result = $menu->getSelectedItem()->getValue();
						$menu->close();
					}
                }
            )
        );

        return $this;
    }

    /**
     * Adds multiple options.
     *
     * @param  array  $options
     * @return \NunoMaduro\LaravelConsoleMenu\Menu
     */
    public function addOptions(array $options): Menu
    {
        foreach ($options as $value => $label) {
            $this->addOption($value, $label);
        }

        return $this;
    }

    /**
     * Add a question.
     *
     * @param  string  $label
     * @param  string  $placeholder
     * @return \NunoMaduro\LaravelConsoleMenu\Menu
     */
    public function addQuestion(string $label, string $placeholder = ''): Menu
    {
        $itemCallable = function (CliMenu $menu) use ($label, $placeholder) {
            $result = $menu->askText()
                ->setPromptText($label)
                ->setPlaceholderText($placeholder)
                ->ask();

            $this->result = $result->fetch();

            $menu->close();
        };

        $this->addItem($label, $itemCallable);

        return $this;
    }

    /**
     * Open the menu and return the result.
     *
     * @return mixed
     */
    public function open()
    {
        $this->build()
            ->open();

        return $this->result;
    }

    /**
     * Set the result.
     *
     * @param  mixed  $result
     * @return \NunoMaduro\LaravelConsoleMenu\Menu
     */
    public function setResult($result): Menu
    {
        $this->result = $result;

        return $this;
    }
}

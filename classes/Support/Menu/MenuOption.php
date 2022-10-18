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

use PhpSchool\CliMenu\MenuItem\SelectableItem;

class MenuOption extends SelectableItem
{
    /**
     * The option value.
     *
     * @var mixed
     */
    private $value;

    /**
     * Creates a new menu option.
     *
     * @param  int|string  $value
     * @param  string  $text
     * @param  callable  $callback
     * @param  bool  $showItemExtra
     * @param  bool  $disabled
     */
    public function __construct($value, $text, callable $callback, $showItemExtra = false, $disabled = false)
    {
        parent::__construct($text, $callback, $showItemExtra, $disabled);

        $this->value = $value;
    }

    /**
     * Returns the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}

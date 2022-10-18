# k3x-cli-tools

A bunch of usefull tools to use with the Kirby CLI.

## Options

You can disable maintenance mode if you don't want your site to go in maintenance mode when there's a `.maintenance` file in the root. Mode can also be (de)activated via `kirby x:kirby:down` and `kirby x:kirby:up`.

The panel will always be reachable, even if the site is in maintenance mode. If you are logged in you will also be able to visit the site.

```php
# site/config/config.php
return [
    'genxbe.k3x-cli-tools' => [
        'maintenance' => true,
    ],
];
```

## Usage

## Installation

### Download

Download and copy this repository to `/site/plugins/k3x-cli-tools`.

### Git submodule

```
git submodule add https://github.com/genxbe/k3x-cli-tools.git site/plugins/k3x-cli-tools
```

### Composer

```
composer require genxbe/k3x-cli-tools
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Sam Serrien](https://twitter.com/samzzi) @ [GeNx](https://genx.be)

## License

Proprietary. Please see [License File](LICENSE.md) for more information.

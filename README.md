# k3x-cli-tools

A bunch of usefull tools to use with the Kirby CLI.

## Options

```php
# site/config/config.php
return [
    'genxbe.k3x-cli-tools' => [
        'maintenance' => true,
    ],
];
```

## Usage

You need to have the kirby cli installed. You can install it via composer:

```bash
composer global require getkirby/cli
```

This CLI plugin has a set of opinionated commands that are meant to be used with the Kirby CLI.

`kirby x:kirby:roots` - Show the roots of your Kirby installation in a pretty way
`kirby x:kirby:down {email}` - Put your site in maintenance mode with an optional email address for contact reasons
`kirby x:kirby:up` - Disable the maintenance mode

`kirby x:plugins:list` - Show the plugins of your kirby installation with version numbers in a pretty way
`kirby x:plugins:delete` - Delete a plugin from your kirby installation (via 'composer remove')
`kirby x:plugins:publish {filetype}` - Publish plugin overwriteable files to your root installation

### Maintenance mode

You can disable maintenance mode if you don't want your site to go in maintenance mode when there's a `.maintenance` file in the root. Mode can also be (de)activated via `kirby x:kirby:down` and `kirby x:kirby:up`.

The panel will always be reachable, even if the site is in maintenance mode. If you are logged in you will also be able to visit the site.

### Plugin publishing

You can publish plugin overwriteable files to your root installation. This is useful if you want to overwrite a plugin file with your own version. You can also use this to publish a plugin file to your root installation if you want to edit it.

This feature expects usage of the folders `blueprints`, `snippets`, `collections` and `templates`. Subfolders within these main folders are also supported.

Filetypes supported:

* blueprints
* snippets
* collections
* templates

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

## License

k3x-cli-tools is an open-sourced software licensed under the [MIT license](LICENSE.md).

## Credits

- [Sam Serrien](https://twitter.com/samzzi) @ [GeNx](https://genx.be)

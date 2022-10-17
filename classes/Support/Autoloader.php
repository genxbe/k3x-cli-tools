<?php

namespace X\Support;

use Spyc;
use Symfony\Component\Finder\Finder;

/**
 * Autoloader class from bnomei
 * https://github.com/bnomei/autoloader-for-kirby
 */
class Autoloader
{
    // exclude files like filename.config.(php|yml)
    public const PHP = '/^[\w\d\-\_]+\.php$/';
    public const ANY_PHP = '/^[\w\d\-\_\.]+\.php$/';
    public const BLOCK_PHP = '/^[\w\d\-\_]+(Block)\.php$/';
    public const PAGE_PHP = '/^[\w\d\-\_]+(Page)\.php$/';
    public const USER_PHP = '/^[\w\d\-\_]+(User)\.php$/';
    public const YML = '/^[\w\d\-\_]+\.yml$/';
    public const ANY_YML = '/^[\w\d\-\_\.]+\.yml$/';
    public const PHP_OR_HTMLPHP = '/^[\w\d\-\_]+(\.html)?\.php$/';
    public const PHP_OR_YML = '/^[\w\d\-\_]+\.(php|yml)$/';
    public const ANY_PHP_OR_YML = '/^[\w\d\-\_\.]+\.(php|yml)$/';
    public const PHP_OR_YML_OR_JSON = '/^[\w\d\-\_]+\.(php|yml|json)$/';
    public const ANY_PHP_OR_YML_OR_JSON = '/^[\w\d\-\_\.]+\.(php|yml|json)$/';

    /** @var self */
    private static $singleton;

    /** @var array */
    private $options;

    /** @var array */
    private $registry;

    public function __construct(array $options = [])
    {
        $this->options = array_merge_recursive([
            'blueprints' => [
                'folder' => 'blueprints',
                'name' => static::ANY_PHP_OR_YML,
                'key' => 'relativepath',
                'lowercase' => true,
            ],
            'collections' => [
                'folder' => 'collections',
                'name' => static::ANY_PHP,
                'key' => 'relativepath',
                'lowercase' => false,
            ],
            'snippets' => [
                'folder' => 'snippets',
                'name' => static::ANY_PHP,
                'key' => 'relativepath',
                'lowercase' => false,
            ],
            'templates' => [
                'folder' => 'templates',
                'name' => static::ANY_PHP,
                'key' => 'relativepath',
                'lowercase' => true,
            ],
        ], $options);

        if (!array_key_exists('dir', $this->options)) {
            throw new \Exception("Autoloader needs a directory to start scanning at.");
        }

        $this->registry = [];
    }

    public function dir(): string
    {
        return $this->options['dir'];
    }

    private function registry(string $type): array
    {
        // only register once
        if (array_key_exists($type, $this->registry)) {
            return $this->registry[$type];
        }

        $options = $this->options[$type];
        $dir = $this->options['dir'] . '/' . $options['folder'];

        if (!file_exists($dir) || !is_dir($dir)) {
            return [];
        }

        $this->registry[$type] = [];

        $finder = (new Finder())->files()
            ->name($options['name'])
            ->in($dir);

        foreach ($finder as $file) {
            $key = '';
            $class = '';
            $split = explode('.', $file->getPathname());
            $extension = array_pop($split);
            if ($options['key'] === 'relativepath' || $options['key'] === 'route') {
                $key = $file->getRelativePathname();
                $key = str_replace('.' . $extension, '', $key);
                $key = str_replace('\\', '/', $key); // windows
                if ($options['lowercase']) {
                    $key = strtolower($key);
                }
            } elseif ($options['key'] === 'filename') {
                $key = basename($file->getRelativePathname());
                $key = str_replace('.' . $extension, '', $key);
                if ($options['lowercase']) {
                    $key = strtolower($key);
                }
            } elseif ($options['key'] === 'classname') {
                $key = $file->getRelativePathname();
                $key = str_replace('.' . $extension, '', $key);
                $class = str_replace('/', '\\', $key);
                if ($classFile = file_get_contents($file->getPathname())) {
                    if (preg_match('/^namespace (.*);$/im', $classFile, $matches) === 1) {
                        $class = str_replace($matches[1] . '\\', '', $class);
                        $class = $matches[1] . '\\' . $class;
                    }
                }
                $this->registry[$type]['map'][$class] = $file->getRelativePathname();

                foreach (['Page', 'User', 'Block'] as $suffix) {
                    $at = strpos($key, $suffix);
                    if ($at === strlen($key) - strlen($suffix)) {
                        $key = substr($key, 0, -strlen($suffix));
                    }
                }
                if ($options['lowercase']) {
                    $key = strtolower($key);
                }
                $this->registry[$type][$key] = $class;
            }
            if (empty($key)) {
                continue;
            } else {
                $key = strval($key); // in case key looks like a number but should be a string
            }

            $this->registry[$type][$key] = $file->getRealPath();
        }

        return $this->registry[$type];
    }

    public function blueprints(): array
    {
        return $this->registry('blueprints');
    }

    public function collections(): array
    {
        return $this->registry('collections');
    }

    public function snippets(): array
    {
        return $this->registry('snippets');
    }

    public function templates(): array
    {
        return $this->registry('templates');
    }

    public function toArray(array $merge = []): array
    {
        $this->classes();

        return array_merge_recursive([
            'blueprints' => $this->blueprints(),
            'collections' => $this->collections(),
            'snippets' => $this->snippets(),
            'templates' => $this->templates(),
        ], $merge);
    }

    public static function singleton(array $options = []): self
    {
        if (self::$singleton && self::$singleton->dir() === $options['dir']) {
            return self::$singleton;
        }
        self::$singleton = new self($options);
        return self::$singleton;
    }

    // https://github.com/getkirby/kirby/blob/c77ccb82944b5fa0e3a453b4e203bd697e96330d/config/helpers.php#L505
    /**
     * A super simple class autoloader
     *
     * @param array $classmap
     * @param string $base
     * @return void
     */
    private function load(array $classmap, string $base = null)
    {
        // convert all classnames to lowercase
        $classmap = array_change_key_case($classmap);

        spl_autoload_register(function ($class) use ($classmap, $base) {
            $class = strtolower($class);

            if (!isset($classmap[$class])) {
                return false;
            }

            if ($base) {
                include $base . '/' . $classmap[$class];
            } else {
                include $classmap[$class];
            }
        });
    }
}

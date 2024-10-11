<?php declare(strict_types=1);

namespace Cloudtay\Nos;

use Cloudtay\Nos\Http\Route\Route;
use Co\IO;
use Psc\Core\File\Exception\FileException;
use Psc\Core\File\Monitor;
use Psc\Core\Stream\Exception\ConnectionException;
use Psc\Utils\Output;
use Psc\Worker\Manager;

use function array_map;
use function file_exists;
use function glob;
use function in_array;
use function pathinfo;
use function scandir;

use const GLOB_BRACE;
use const PATHINFO_EXTENSION;

class Kernel
{
    /*** @var string */
    public static string $routeClass = Route::class;

    /*** @var Manager */
    public static Manager $manager;

    /*** @var string */
    public static string $appPath;

    /***
     * @param string $appPath
     * @return void
     */
    public static function initialize(string $appPath): void
    {
        static::$appPath = $appPath;
        static::$manager = new Manager();
        foreach (scandir($appPath) as $path) {
            if (in_array($path, ['.', '..'])) {
                continue;
            }

            Kernel::import($path);
        }
    }

    /*** @return void */
    public static function run(): void
    {
        \Co\forked(static function () {
            array_map(
                static fn ($file) => require_once $file,
                glob(static::$appPath . '/**/*.php', GLOB_BRACE)
            );
        });

        $monitor = new Monitor();
        $monitor->add(static::$appPath);
        $monitor->onTouch  = static fn () => static::reload();
        $monitor->onModify = static fn () => static::reload();
        $monitor->onRemove = static fn () => static::reload();
        $monitor->start();

        static::$manager->run();
    }

    /**
     * @return void
     */
    public static function reload(): void
    {
        try {
            static::$manager->reload();
        } catch (ConnectionException $e) {
            Output::exception($e);
            exit(-1);
        }
    }

    /**
     * @var array
     */
    private static array $modules = [];

    /**
     * @param string $module
     * @return mixed
     */
    public static function import(string $module): mixed
    {
        if (isset(static::$modules[$module])) {
            return static::$modules[$module];
        }

        if (file_exists($modulePath = static::getModulePath($module))) {
            return static::$modules[$module] = require $modulePath;
        } elseif (file_exists($module)) {
            if (pathinfo($module, PATHINFO_EXTENSION) === 'php') {
                return static::$modules[$module] = require $module;
            } else {
                try {
                    return static::$modules[$module] = IO::File()->getContents($module);
                } catch (FileException $e) {
                    Output::warning("module {$module} load fail: {$e->getMessage()}");
                    return false;
                }
            }
        }


        Output::warning("Module {$module} not found");
        return false;
    }

    /**
     * @param string $module
     * @return string
     */
    public static function getModulePath(string $module): string
    {
        return static::$appPath . "/{$module}/{$module}.php";
    }
}

<?php declare(strict_types=1);

namespace Cloudtay\Nos;

use Psc\Utils\Output;

use function array_shift;
use function array_unshift;
use function debug_backtrace;
use function file_get_contents;
use function is_dir;
use function is_file;
use function pathinfo;
use function realpath;
use function str_ends_with;

use const PATHINFO_BASENAME;
use const PATHINFO_DIRNAME;
use const PATHINFO_EXTENSION;

class Package
{
    /**
     * 绝对路径 => 模块
     *
     * @var array
     */
    private static array $publicMap = [];

    /**
     * [域][模块] = 模块
     *
     * @var array
     */
    private static array $ownersMap = [];

    /**
     * @param string $module
     * @param bool   $quiet
     *
     * @return mixed
     */
    public static function import(string $module, bool $quiet = false): mixed
    {
        $ownerFile = debug_backtrace()[0]['file'];
        $ownerPath = pathinfo($ownerFile, PATHINFO_DIRNAME);
        if (isset(Package::$ownersMap[$ownerPath][$module])) {
            return Package::$ownersMap[$ownerPath][$module];
        }

        $ownerIndexList = [
            $ownerPath . "/{$module}",
            $ownerPath . "/{$module}.php",
            $ownerPath . "/{$module}/{$module}.php",
        ];

        while ($packagePath = array_shift($ownerIndexList)) {
            if (is_file($packagePath)) {
                if (isset(Package::$publicMap[realpath($packagePath)])) {
                    return Package::$publicMap[realpath($packagePath)];
                }

                $package                                    = Package::require($packagePath);
                Package::$ownersMap[$ownerPath][$module]    = $package;
                Package::$publicMap[realpath($packagePath)] = $package;
                return $package;
            }
        }

        $publicIndexList = [
            NOS_APP_PATH . "/{$module}",
            NOS_APP_PATH . "/{$module}.php",
            NOS_APP_PATH . "/{$module}/{$module}.php",
        ];

        if (is_dir($module)) {
            $moduleBaseName    = pathinfo($module, PATHINFO_BASENAME);
            array_unshift($publicIndexList, "{$module}/{$moduleBaseName}.php");
        } elseif (is_file($module)) {
            array_unshift($publicIndexList, $module);
        }

        while ($modulePath = array_shift($publicIndexList)) {
            if (is_file($modulePath)) {
                if (isset(Package::$publicMap[realpath($modulePath)])) {
                    return Package::$publicMap[realpath($modulePath)];
                }
                return Package::$publicMap[realpath($modulePath)] = Package::require(realpath($modulePath));
            }
        }

        $quiet || Output::warning("Module {$module} not found");
        return false;
    }


    /**
     * @param string $path
     *
     * @return mixed
     */
    private static function require(string $path): mixed
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if ($extension === 'php') {
            if (str_ends_with($path, '.blade.php')) {
                goto plaintext;
            } elseif (str_ends_with($path, '.template.php')) {
                goto plaintext;
            }
            return require $path;
        }

        plaintext:
        return file_get_contents($path);
    }
}

<?php declare(strict_types=1);

namespace Cloudtay\Nos;

use DirectoryIterator;
use Psc\Core\File\Monitor;
use Psc\Core\Stream\Exception\ConnectionException;
use Psc\Utils\Output;
use Psc\Worker\Manager;

use function str_starts_with;

class Kernel
{
    /*** @var Manager */
    protected static Manager $manager;

    /*** @var \Psc\Core\File\Monitor */
    protected static Monitor $monitor;

    /***
     *
     * @return void
     */
    public static function initialize(): void
    {
        Kernel::$monitor = new Monitor();
        Kernel::$monitor->add(NOS_APP_PATH);
        Kernel::$monitor->onTouch  = static fn () => Kernel::reload();
        Kernel::$monitor->onModify = static fn () => Kernel::reload();
        Kernel::$monitor->onRemove = static fn () => Kernel::reload();

        Kernel::$manager = new Manager();

        /*** @var DirectoryIterator $fileInfo */
        foreach ((new DirectoryIterator(NOS_APP_PATH)) as $fileInfo) {
            if (str_starts_with($fileInfo->getFilename(), '.')) {
                continue;
            }

            Package::import($fileInfo->getPathname(), true);
        }
    }

    /**
     * @return void
     */
    public static function run(): void
    {
        Kernel::$monitor->start();
        Kernel::$manager->run();
    }

    /**
     * @return void
     */
    public static function reload(): void
    {
        try {
            Kernel::$manager->reload();
        } catch (ConnectionException $e) {
            Output::exception($e);
            exit(-1);
        }
    }

    /**
     * @return \Psc\Core\File\Monitor
     */
    public static function monitor(): Monitor
    {
        return Kernel::$monitor;
    }

    /**
     * @return \Psc\Worker\Manager
     */
    public static function manager(): Manager
    {
        return Kernel::$manager;
    }

    /**
     * @param string|null $path
     *
     * @return string
     */
    public static function appPath(string $path = null): string
    {
        if (!$path) {
            return NOS_APP_PATH;
        } elseif ($path[0] === '/') {
            return NOS_APP_PATH . $path;
        } else {
            return NOS_APP_PATH . '/' . $path;
        }
    }
}

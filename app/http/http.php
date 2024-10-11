<?php declare(strict_types=1);

use Cloudtay\Nos\Kernel;
use Co\Net;
use Psc\Core\Http\Server\Request;
use Psc\Core\Http\Server\Server;
use Psc\Worker\Manager;

$worker = new class () extends \Psc\Worker\Worker {
    /*** @var string */
    protected string $listen = 'http://127.0.0.1:8008';

    /*** @var int */
    protected int $count = 8;

    /*** @var string */
    protected string $name = 'http-server';

    /*** @var Server */
    private Psc\Core\Http\Server\Server $server;

    /**
     * @param Manager $manager
     * @return void
     */
    public function register(Manager $manager): void
    {
        $this->server = Net::Http()->server($this->listen, [
            'socket' => [
                'so_reuseport' => 1,
                'so_reuseaddr' => 1
            ]
        ]);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->server->onRequest(
            static fn (Request $request) => Kernel::$routeClass::dispatch($request)
        );
        $this->server->listen();
    }
};

Kernel::$manager->addWorker($worker);
return $worker;

<?php declare(strict_types=1);

use Cloudtay\Nos\Kernel;
use Co\Net;
use Psc\Core\WebSocket\Server\Connection;
use Psc\Core\WebSocket\Server\Server;
use Psc\Worker\Command;
use Psc\Worker\Manager;

Kernel::$manager->addWorker(new class () extends \Psc\Worker\Worker {
    /*** @var string */
    protected string $listen = 'ws://127.0.0.1:8001';

    /*** @var string */
    protected string $name = 'ws-server';

    /*** @var Server */
    private Server $server;

    /**
     * @param Manager $manager
     * @return void
     */
    public function register(Manager $manager): void
    {
        $this->server = Net::WebSocket()->server($this->listen);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->server->listen();
        $this->server->onConnect(static function (Connection $connection) {
            $connection->send('welcome');
        });

        $this->server->onMessage(static function (string $content, Connection $connection) {
            $connection->send("received: {$content}");
        });
    }

    /**
     * @param Command $workerCommand
     * @return void
     */
    public function onCommand(Command $workerCommand): void
    {
        if ($workerCommand->name === 'message') {
            $this->server->broadcast($workerCommand->arguments[0]);
        }
    }
});

<?php declare(strict_types=1);

use Cloudtay\Nos\Http\Method;
use Cloudtay\Nos\Http\Route\Route;
use Cloudtay\Nos\Kernel;
use Psc\Core\Http\Server\Chunk;
use Psc\Core\Http\Server\Request;
use Psc\Worker\Command;

/**
 * @documents
 * @var \Psc\Worker\Worker $http
 * @var string             $http
 */

$http          = Kernel::import('http');
$indexTemplate = Kernel::import(__DIR__ . '/view/index.html');

/**
 * @method GET
 * @path /
 */
Route::define(Method::GET, '/', static function (Request $request) use ($indexTemplate) {
    $request->respond(
        $indexTemplate,
        200,
        ['Content-Type' => 'text/html']
    );
});

/**
 * @method GET
 * @path /hello
 */
Route::define(
    Method::GET,
    '/hello',
    static function (Request $request) {
        $request->respond('Hello, World!');
    }
);

/**
 * @method POST
 * @path /send
 */
Route::define(
    Method::POST,
    '/send',
    static function (Request $request) use ($http) {

        if ($message = $request->POST['message'] ?? null) {
            $command = Command::make('message', [$message]);
            $http->commandToWorker($command, 'ws-server');
            $request->respond(
                \json_encode(
                    ['message' => 'Message sent!']
                ),
                200,
                ['Content-Type' => 'application/json']
            );
            return;
        }

        $request->respond(
            \json_encode(
                ['error' => 'Message is required!']
            ),
            200,
            ['Content-Type' => 'application/json']
        );
    }
);

/**
 * @method GET
 * @path /stream
 */
Route::define(
    Method::GET,
    '/stream',
    static function (Request $request) {
        $content = static function () {
            for ($i = 0; $i < 10; $i++) {
                yield Chunk::event('message', 'Hello, World!', \strval($i));
                \Co\sleep(1);
            }

            return false;
        };

        $request->respond($content(), 200, [
            'Content-Type'  => 'text/event-stream',
            'Cache-Control' => 'no-cache',
        ]);
    }
);

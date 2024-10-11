<?php declare(strict_types=1);

use Cloudtay\Nos\Http\Method;
use Cloudtay\Nos\Http\Route\Route;
use Cloudtay\Nos\Kernel;
use Psc\Core\Http\Server\Request;

$favicon = Kernel::import(__DIR__ . '/view/favicon.ico');

/**
 * @method GET
 * @path /favicon.ico
 */
Route::define(
    Method::GET,
    '/favicon.ico',
    static function (Request $request) use ($favicon) {
        $request->respond($favicon, 200, ['Content-Type' => 'image/x-icon']);
    }
);

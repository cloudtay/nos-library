<?php declare(strict_types=1);


use Cloudtay\Nos\Http\Method;
use Cloudtay\Nos\Http\Route\Route;
use Co\IO;
use Psc\Core\Http\Server\Request;

/**
 * @method GET
 * @path /download
 */
Route::define(
    Method::GET,
    '/download',
    static function (Request $request) {
        $request->respond(IO::File()->open(__FILE__, 'r'));
    }
);

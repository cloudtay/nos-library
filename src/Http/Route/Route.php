<?php declare(strict_types=1);

namespace Cloudtay\Nos\Http\Route;

use Closure;
use Cloudtay\Nos\Http\Status;
use Exception;
use Psc\Core\Http\Server\Request;
use Psc\Utils\Output;

use function trim;

class Route
{
    /*** @var array */
    protected static array $map = [];

    /**
     * @param string  $method
     * @param string  $uri
     * @param Closure $handler
     * @return void
     */
    public static function define(string $method, string $uri, Closure $handler): void
    {
        static::$map[$method][trim($uri, '/')] = $handler;
    }

    /**
     * @param Request $request
     * @return void
     */
    public static function dispatch(Request $request): void
    {
        if ($handler = static::match(
            $request->SERVER['REQUEST_METHOD'],
            trim($request->SERVER['REQUEST_URI'], '/')
        )) {
            try {
                $handler($request);
            } catch (Exception $e) {
                Output::error($e->getMessage());
                $request->respond(
                    Status::MESSAGES[Status::INTERNAL_SERVER_ERROR],
                    Status::INTERNAL_SERVER_ERROR
                );
            }
        } else {
            $request->respond(
                Status::MESSAGES[Status::NOT_FOUND],
                Status::NOT_FOUND
            );
        }
    }

    /**
     * @param string $method
     * @param string $uri
     * @return Closure|null
     */
    public static function match(string $method, string $uri): Closure|null
    {
        return static::$map[$method][$uri] ?? null;
    }
}

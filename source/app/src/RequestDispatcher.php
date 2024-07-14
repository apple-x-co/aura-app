<?php

declare(strict_types=1);

namespace MyVendor\MyPackage;

use Aura\Di\Container;
use Koriym\HttpConstants\StatusCode;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequestFactory;
use MyVendor\MyPackage\Exception\RuntimeException;
use MyVendor\MyPackage\Renderer\HtmlRenderer;
use MyVendor\MyPackage\Renderer\JsonRenderer;
use MyVendor\MyPackage\Renderer\TextRenderer;
use MyVendor\MyPackage\RequestHandler\AbstractRequestHandler;
use MyVendor\MyPackage\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use function class_exists;
use function is_callable;
use function is_string;
use function method_exists;
use function sprintf;
use function str_contains;
use function strtolower;
use function ucfirst;

final class RequestDispatcher
{
    public function __construct(
        private readonly AppMeta $appMeta,
        private readonly Container $di,
        private readonly RouterInterface $router
    ) {
    }

    public function __invoke(): ResponseInterface|null
    {
        $origServerRequest = ServerRequestFactory::fromGlobals();

        $routerMatch = $this->router->match($origServerRequest);
        $route = $routerMatch->route;
        if ($route === false) {
            return new TextResponse(
                'Route not found.',
                StatusCode::NOT_FOUND,
                [],
            );
        }

        $serverRequest = $routerMatch->serverRequest ?? $origServerRequest;

        foreach ($route->attributes as $name => $value) {
            $serverRequest = $serverRequest->withAttribute($name, $value);
        }

        $routeHandler = $route->handler;
        if (is_callable($routeHandler)) {
            return $routeHandler($serverRequest);
        }

        if (! is_string($routeHandler)) {
            return null;
        }

        if (! class_exists($routeHandler)) {
            throw new RuntimeException('Route handler "' . $routeHandler . '" not found.');
        }

        $object = $this->di->newInstance($routeHandler);
        $action = sprintf('on%s', ucfirst(strtolower($routerMatch->method)));

        if (! method_exists($object, $action)) {
            return new TextResponse(
                'Method not allowed.',
                StatusCode::METHOD_NOT_ALLOWED,
                [],
            );
        }

        try {
            $object = $object->$action($routerMatch->serverRequest ?? $serverRequest);
            if (! $object instanceof AbstractRequestHandler) {
                return new EmptyResponse();
            }

            if (isset($object->headers['location'])) {
                return new RedirectResponse(
                    $object->headers['location'],
                    $object->code,
                    $object->headers,
                );
            }

            $renderer = $object->renderer;
            if ($renderer === null) {
                $accepts = $serverRequest->getHeader('accept');
                if (! empty($accepts) && str_contains($accepts[0], 'text/html')) {
                    $renderer = $this->di->newInstance(HtmlRenderer::class);
                } elseif (! empty($accepts) && str_contains($accepts[0], 'application/json')) {
                    $renderer = $this->di->newInstance(JsonRenderer::class);
                } else {
                    $renderer = $this->di->newInstance(TextRenderer::class);
                }
            }

            $response = new Response();
            $response->getBody()->write($renderer->render($object));
            foreach ($object->headers as $name => $value) {
                $response = $response->withHeader($name, $value);
            }
            $response = $response->withStatus($object->code);
        } catch (Throwable $throwable) {
            return new TextResponse(
                (string) $throwable,
                StatusCode::INTERNAL_SERVER_ERROR,
                [],
            );
        }

        return $response;
    }
}

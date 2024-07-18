<?php

declare(strict_types=1);

namespace MyVendor\MyPackage;

use Aura\Di\Container;
use Koriym\HttpConstants\StatusCode;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\TextResponse;
use MyVendor\MyPackage\Exception\RuntimeException;
use MyVendor\MyPackage\Renderer\HtmlRenderer;
use MyVendor\MyPackage\Renderer\JsonRenderer;
use MyVendor\MyPackage\Renderer\TextRenderer;
use MyVendor\MyPackage\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
        private readonly Container $di,
        private readonly RouterInterface $router,
        private readonly ServerRequestInterface $serverRequest,
        private readonly HtmlRenderer $htmlRenderer,
        private readonly JsonRenderer $jsonRenderer,
        private readonly TextRenderer $textRenderer,
    ) {
    }

    public function __invoke(): ResponseInterface|null
    {
        $routerMatch = $this->router->match($this->serverRequest);
        $route = $routerMatch->route;
        if ($route === false) {
            return new TextResponse(
                'Route not found.',
                StatusCode::NOT_FOUND,
                [],
            );
        }

        $serverRequest = $routerMatch->serverRequest ?? $this->serverRequest;

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
        if (! $object instanceof RequestHandler) {
            throw new RuntimeException('Route handler "' . $routeHandler . '" not found.');
        }

        if ($route->auth['admin'] ?? false) {
            // TODO: Check ADMIN authentication
        }

        $action = sprintf('on%s', ucfirst(strtolower($routerMatch->method)));
        if (! method_exists($object, $action)) {
            throw new RuntimeException('Method not allowed.');
        }

        try {
            $object = $object->$action($routerMatch->serverRequest ?? $serverRequest);
            if (! $object instanceof RequestHandler) {
                throw new RuntimeException('Invalid response type.');
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
                    $renderer = $this->htmlRenderer;
                } elseif (! empty($accepts) && str_contains($accepts[0], 'application/json')) {
                    $renderer = $this->jsonRenderer;
                } else {
                    $renderer = $this->textRenderer;
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

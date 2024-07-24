<?php

declare(strict_types=1);

namespace MyVendor\MyPackage;

use Aura\Accept\Accept;
use Aura\Accept\Media\MediaValue;
use Aura\Di\Container;
use Koriym\HttpConstants\MediaType;
use Koriym\HttpConstants\StatusCode;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\TextResponse;
use MyVendor\MyPackage\Auth\AdminAuthenticationHandler;
use MyVendor\MyPackage\Auth\AdminAuthenticationRequestHandlerInterface;
use MyVendor\MyPackage\Auth\AuthenticationException;
use MyVendor\MyPackage\Captcha\CaptchaException;
use MyVendor\MyPackage\Captcha\CloudflareTurnstileVerificationHandler;
use MyVendor\MyPackage\Captcha\CloudflareTurnstileVerificationRequestHandlerInterface;
use MyVendor\MyPackage\Form\FormValidationInterface;
use MyVendor\MyPackage\Renderer\HtmlRenderer;
use MyVendor\MyPackage\Renderer\JsonRenderer;
use MyVendor\MyPackage\Renderer\RendererInterface;
use MyVendor\MyPackage\Renderer\TextRenderer;
use MyVendor\MyPackage\Router\InvalidResponseException;
use MyVendor\MyPackage\Router\RouteHandlerMethodNotAllowedException;
use MyVendor\MyPackage\Router\RouteHandlerNotFoundException;
use MyVendor\MyPackage\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function assert;
use function class_exists;
use function is_callable;
use function is_string;
use function method_exists;
use function sprintf;
use function strtolower;
use function ucfirst;

final class RequestDispatcher
{
    public function __construct(
        private readonly Accept $accept,
        private readonly AdminAuthenticationHandler $adminAuthenticationHandler,
        private readonly CloudflareTurnstileVerificationHandler $cloudflareTurnstileVerificationHandler,
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
                'Route not found :(',
                StatusCode::NOT_FOUND,
                [],
            );
        }

        $serverRequest = $routerMatch->serverRequest;

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
            throw new RouteHandlerNotFoundException('Route handler "' . $routeHandler . '" not found  :(');
        }

        $object = $this->di->newInstance($routeHandler);
        if (! $object instanceof RequestHandler) {
            throw new RouteHandlerNotFoundException('Route handler "' . $routeHandler . '" not found  :(');
        }

        // NOTE: Cloudflare turnstile verify
        try {
            ($this->cloudflareTurnstileVerificationHandler)($routerMatch);
        } catch (CaptchaException $captchaException) {
            if ($object instanceof CloudflareTurnstileVerificationRequestHandlerInterface) {
                $object = $object->onCfTurnstileFailed($captchaException);
            }

            return $this->getResponse($object);
        }

        // NOTE: Form validation
        if ($object instanceof FormValidationInterface) {
            $isValid = $object->formValidate($serverRequest);
            if (! $isValid) {
                $object = $object->onFormValidationFailed();

                return $this->getResponse($object);
            }
        }

        // NOTE: Admin authentication
        try {
            $adminAuthenticationResponse = ($this->adminAuthenticationHandler)($routerMatch);
        } catch (AuthenticationException $authenticationException) {
            if ($object instanceof AdminAuthenticationRequestHandlerInterface) {
                $object = $object->onAuthenticationFailed($authenticationException);
            }

            return $this->getResponse($object);
        }

        if ($adminAuthenticationResponse !== null) {
            return $adminAuthenticationResponse;
        }

        // NOTE: Request handling
        $action = sprintf('on%s', ucfirst(strtolower($routerMatch->method)));
        if (! method_exists($object, $action)) {
            throw new RouteHandlerMethodNotAllowedException('Method not allowed.');
        }

        try {
            // NOTE: RequestHandler で ServerRequest や Route の取得をしたい場合は "Typehinted constructor" を使う
            $object = $object->$action();
            if (! $object instanceof RequestHandler) {
                throw new InvalidResponseException('Invalid response type.');
            }

            if (isset($object->headers['location'])) {
                return new RedirectResponse(
                    $object->headers['location'],
                    $object->code,
                    $object->headers,
                );
            }

            $response = $this->getResponse($object);
        } catch (Throwable $throwable) {
            return new TextResponse(
                (string) $throwable,
                StatusCode::INTERNAL_SERVER_ERROR,
                [],
            );
        }

        return $response;
    }

    private function getResponse(RequestHandler $object): ResponseInterface
    {
        $renderer = $object->renderer ?? $this->getRenderer();
        assert($renderer instanceof RendererInterface);

        $response = new Response();
        $response->getBody()->write($renderer->render($object));
        foreach ($object->headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response->withStatus($object->code);
    }

    private function getRenderer(): RendererInterface
    {
        $media = $this->accept->negotiateMedia([
            MediaType::TEXT_HTML,
            MediaType::APPLICATION_JSON,
            MediaType::TEXT_PLAIN,
        ]);

        if (
            $media instanceof MediaValue &&
            $media->getValue() === MediaType::TEXT_HTML
        ) {
            return $this->htmlRenderer;
        }

        if (
            $media instanceof MediaValue &&
            $media->getValue() === MediaType::APPLICATION_JSON
        ) {
            return $this->jsonRenderer;
        }

        return $this->textRenderer;
    }
}

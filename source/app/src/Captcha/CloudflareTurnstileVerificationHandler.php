<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Captcha;

use Laminas\Http\Client as LaminasHttpClient;
use Laminas\Http\Client\Adapter\Socket as LaminasSocket;
use Laminas\Http\Request;
use MyVendor\MyPackage\Router\RouterMatch;

use function is_array;
use function is_bool;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final class CloudflareTurnstileVerificationHandler implements CloudflareTurnstileVerificationHandlerInterface
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct(
        private readonly string $secretKey,
    ) {
    }

    public function __invoke(RouterMatch $routerMatch): void
    {
        if (! $this->isCfTurnstile($routerMatch)) {
            return;
        }

        $cfToken = $_POST['cf-turnstile-response'] ?? null;
        if ($cfToken === null) {
            throw new CaptchaTokenMissing();
        }

        $adapter = new LaminasSocket();
        $client = new LaminasHttpClient(
            self::VERIFY_URL,
            [
                'maxredirects' => 0,
                'timeout' => 30,
            ],
        );
        $client->setMethod(Request::METHOD_POST);
        $client->setParameterPost([
            'secret' => $this->secretKey,
            'response' => $cfToken,
            'remoteip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
        $client->setAdapter($adapter);
        $adapter->setStreamContext([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ]);

        $response = $client->send();

        if ($response->getStatusCode() !== 200) {
            throw new CaptchaTokenMissing();
        }

        /** @var array{success: bool, challenge_ts?: string, hostname?: string, error-codes?: array<string>, action?: string, cdata?: string} $result */
        $result = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if ($result['success']) {
            return;
        }

        throw new CaptchaVerifyError();
    }

    private function isCfTurnstile(RouterMatch $routerMatch): bool
    {
        if ($routerMatch->route === false) {
            return false;
        }

        $auth = $routerMatch->route->auth;

        return is_array($auth) &&
            isset($auth['cfTurnstile']) &&
            is_bool($auth['cfTurnstile']) &&
            $auth['cfTurnstile'];
    }
}

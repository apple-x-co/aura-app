<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Captcha;

use MyVendor\MyPackage\Router\RouterMatch;

use function curl_close;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt_array;
use function http_build_query;
use function is_array;
use function is_bool;
use function json_decode;

use const CURLINFO_HTTP_CODE;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_SSL_VERIFYHOST;
use const CURLOPT_SSL_VERIFYPEER;
use const CURLOPT_TIMEOUT;
use const CURLOPT_URL;
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

        $data = [
            'secret' => $this->secretKey,
            'response' => $cfToken,
            'remoteip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        $options = [
            CURLOPT_URL => self::VERIFY_URL,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = (string) curl_exec($curl);
        $code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($code !== 200) {
            throw new CaptchaTokenMissing();
        }

        /** @var array{success: bool, challenge_ts?: string, hostname?: string, error-codes?: array<string>, action?: string, cdata?: string} $result */
        $result = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

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

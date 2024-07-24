<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Handler\Admin;

use MyVendor\MyPackage\Auth\AdminAuthenticationRequestHandlerInterface;
use MyVendor\MyPackage\Auth\AuthenticationException;
use MyVendor\MyPackage\Captcha\CaptchaException;
use MyVendor\MyPackage\Captcha\CloudflareTurnstileVerificationRequestHandlerInterface;
use MyVendor\MyPackage\Form\Admin\LoginForm;
use MyVendor\MyPackage\Form\FormValidationInterface;
use MyVendor\MyPackage\RequestHandler;
use Psr\Http\Message\ServerRequestInterface;

final class Login extends RequestHandler implements AdminAuthenticationRequestHandlerInterface,
                                                    CloudflareTurnstileVerificationRequestHandlerInterface,
                                                    FormValidationInterface
{
    public function __construct(private readonly LoginForm $form)
    {
        $this->body['form'] = $this->form;
    }

    public function onGet(): self
    {
        return $this;
    }

    public function onAuthenticationFailed(AuthenticationException $authenticationException): self
    {
        $this->body['authError'] = true;

        return $this;
    }

    public function onCfTurnstileFailed(CaptchaException $captchaException): self
    {
        $this->body['captchaError'] = true;

        return $this;
    }

    public function formValidate(ServerRequestInterface $serverRequest): bool
    {
        $this->form->fill($serverRequest->getParsedBody());

        return $this->form->filter();
    }

    public function onFormValidationFailed(): FormValidationInterface
    {
        $this->body['formError'] = true;

        return $this;
    }
}

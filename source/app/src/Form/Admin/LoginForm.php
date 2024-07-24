<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Form\Admin;

use MyVendor\MyPackage\Form\ExtendedForm;

use function preg_match;

final class LoginForm extends ExtendedForm
{
    public function init(): void
    {
        $filter = $this->getFilter();

        /** @psalm-suppress UndefinedMethod */
        $this->setField('username', 'text')
            ->setAttribs([
                'autofocus' => '',
                'autocomplete' => 'username',
                'placeholder' => 'username',
                'required' => 'required',
                'title' => '有効なユーザー名を入力してください',
            ]);
        $filter->setRule(
            'username',
            'ログインIDを入力してください',
            static fn ($value) => $value !== ''
        );

        /** @psalm-suppress UndefinedMethod */
        $this->setField('password', 'password')
            ->setAttribs([
                'autocomplete' => 'current-password',
                'placeholder' => 'password',
                'required' => 'required',
                'title' => '有効なパスワードを入力してください',
            ]);
        $filter->setRule(
            'password',
            'パスワードを入力してください',
            static fn ($value) => (bool) preg_match('/^[A-Za-z0-9!@#$%^&*]+$/i', $value)
        );

        /** @psalm-suppress UndefinedMethod */
        $this->setField('continue', 'submit')
            ->setAttribs(['value' => 'Login']);
        $filter->setRule(
            'continue',
            'Login をクリックしてください',
            static fn ($value) => $value === 'Login'
        );
    }
}

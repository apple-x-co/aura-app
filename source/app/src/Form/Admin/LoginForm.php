<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Form\Admin;

use MyVendor\MyPackage\Form\ExtendedForm;

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
//        $this->filter
//            ->validate('username')
//            ->is('email');
//        $this->filter->useFieldMessage('username', '有効なEメールアドレスを入力してください');
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
//        /** @psalm-suppress TooManyArguments */
//        $this->filter
//            ->validate('password')
//            ->is('string');
//        /** @psalm-suppress TooManyArguments */
//        $this->filter
//            ->validate('password')
//            ->is('regex', '/^[A-Za-z0-9!@#$%^&*]+$/i');
//        $this->filter->useFieldMessage('password', '有効なパスワードを入力してください');
        $filter->setRule(
            'password',
            'パスワードを入力してください',
            static fn ($value) => $value !== ''
        );

        /** @psalm-suppress UndefinedMethod */
        $this->setField('continue', 'submit')
            ->setAttribs(['value' => 'Login']);
        $filter->setRule(
            'continue',
            'Login をクリックしてください',
            static fn ($value) => $value === 'Login'
        );

//         /** @psalm-suppress TooManyArguments */
//        $this->filter
//            ->validate('login')
//            ->is('string');
//        /** @psalm-suppress TooManyArguments */
//        $this->filter
//            ->validate('login')
//            ->is('strictEqualToValue', 'ログイン');
//        $this->filter->useFieldMessage(
//            'login',
//            'ログインをクリックしてください',
//        );

    }
}

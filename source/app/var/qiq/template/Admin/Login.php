<html lang="ja">
<head><title>Login | Admin</title></head>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script>
    function cfTurnstileChecked() {
        document.getElementById('continue').removeAttribute('disabled');
    }
    function cfTurnstileExpired() {
        document.getElementById('continue').setAttribute('disabled', 'disabled');
    }
    function cfTurnstileError() {
        document.getElementById('continue').setAttribute('disabled', 'disabled');
    }
    function cfTurnstileTimeout() {
        document.getElementById('continue').setAttribute('disabled', 'disabled');
    }
</script>
<body>
<h1>Login</h1>
<form method="post">
    {{ if (isset($authError) && $authError): }}
    <p>Authentication Error</p>
    {{ endif }}
    {{ if (isset($captchaError) && $captchaError): }}
    <p>Captcha Error</p>
    {{ endif }}
    {{ if (isset($formError) && $formError): }}
    <p>Input Error</p>
    {{ endif }}

    {{= $form->widget('username', attr: ['class' => 'abc']) }}
    {{ foreach ($form->getMessages('username') as $errorMessage): }}
    <p>{{h $errorMessage }}</p>
    {{ endforeach; }}

    {{= $form->widget('password', attr: ['class' => 'abc']) }}
    {{ foreach ($form->getMessages('password') as $errorMessage): }}
    <p>{{h $errorMessage }}</p>
    {{ endforeach; }}

    {{= cfTurnstileWidget(action: 'login', checked: 'cfTurnstileChecked', expired: 'cfTurnstileExpired', error: 'cfTurnstileError', timeout: 'cfTurnstileTimeout') }}

    {{= $form->widget('continue', attr: ['id' => 'continue', 'disabled' => 'disabled']) }}
    {{ foreach ($form->getMessages('continue') as $errorMessage): }}
    <p>{{h $errorMessage }}</p>
    {{ endforeach; }}
</form>
</body>
</html>

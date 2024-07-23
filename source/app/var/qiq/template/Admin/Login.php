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
    <input type="text" name="username" value="" required minlength="5" placeholder="username">
    <input type="password" name="password" value="" required minlength="5" placeholder="password">
    {{= cfTurnstileWidget(action: 'login', checked: 'cfTurnstileChecked', expired: 'cfTurnstileExpired', error: 'cfTurnstileError', timeout: 'cfTurnstileTimeout') }}
    <button id="continue" disabled="disabled">Login</button>
</form>
</body>
</html>

<html lang="ja">
<head><title>Login | Admin</title></head>
<body>
<h1>Login</h1>
<form method="post">
    {{ if (isset($authError) && $authError): }}
    <p>Authentication Error</p>
    {{ endif }}
    <input type="text" name="username" value="" required minlength="5" placeholder="username">
    <input type="password" name="password" value="" required minlength="5" placeholder="password">
    <button>Login</button>
</form>
</body>
</html>

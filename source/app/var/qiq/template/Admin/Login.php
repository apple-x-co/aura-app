{{ extends ('layout/Admin/base') }}

{{ setBlock ('title') }}Login | {{ parentBlock () }}{{ endBlock () }}

{{ setBlock ('head_scripts') }}
{{ parentBlock () }}
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
{{ endBlock () }}

{{ setBlock ('body') }}
<div class="h-[100svh] grid place-content-center">
    <div class="relative w-80 md:w-96 h-min p-5 md:p-8 rounded-xl bg-white shadow-[0_1px_3px_rgba(15,23,42,0.03),0_1px_2px_rgba(15,23,42,0.06)] ring-1 ring-slate-600/[0.04]">
        <h2 class="text-xl text-center tracking-widest font-black bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">AuraApp</h2>

        <form method="post">
            {{ if (isset($authError) && $authError): }}
            {{= render('partials/Admin/AlertError', ['text' => 'Authentication error']) }}
            {{ endif }}
            {{ if (isset($captchaError) && $captchaError): }}
            {{= render('partials/Admin/AlertError', ['text' => 'CAPTCHA error']) }}
            {{ endif }}
            {{ if (isset($formError) && $formError): }}
            {{= render('partials/Admin/AlertError', ['text' => 'Input error']) }}
            {{ endif }}
            <div class="mt-5">
                <label class="block">
                    <span class="block text-sm font-sans font-normal text-slate-700 select-none">Username</span>
                    {{= $form->widget('username', attr: ['class' => 'rounded w-full placeholder:text-slate-500 placeholder:font-thin focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none invalid:border-pink-500 invalid:text-pink-600 focus:invalid:border-pink-500 focus:invalid:ring-pink-500']) }}
                    {{ foreach ($form->errorMessages('username') as $errorMessage): }}
                    <p class="block text-sm text-rose-500 italic">{{h $errorMessage }}</p>
                    {{ endforeach; }}
                </label>
                <label class="block mt-5">
                    <span class="block text-sm font-sans font-normal text-slate-700 select-none">Password</span>
                    {{= $form->widget('password', attr: ['class' => 'rounded w-full placeholder:text-slate-500 placeholder:font-thin focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none invalid:border-pink-500 invalid:text-pink-600 focus:invalid:border-pink-500 focus:invalid:ring-pink-500']) }}
                    {{ foreach ($form->errorMessages('password') as $errorMessage): }}
                    <p class="block text-sm text-rose-500 italic">{{h $errorMessage }}</p>
                    {{ endforeach; }}
                </label>
                <div class="ml-[-11px] lg:ml-0 mt-5">
                    {{= cfTurnstileWidget(action: 'login', checked: 'cfTurnstileChecked', expired: 'cfTurnstileExpired', error: 'cfTurnstileError', timeout: 'cfTurnstileTimeout') }}
                </div>
                <label class="block mt-5 text-center">
                    {{= $form->csrfTokenWidget() }}
                    {{= $form->widget('continue', attr: ['id' => 'continue', 'disabled' => 'disabled', 'class' => 'py-2 px-4 bg-indigo-500 text-white text-sm font-sans font-bold tracking-wider rounded-full shadow-lg shadow-indigo-500/50 focus:outline-none hover:bg-indigo-600 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none']) }}
                    {{ foreach ($form->getMessages('continue') as $errorMessage): }}
                    <p class="block text-sm text-rose-500 italic">{{h $errorMessage }}</p>
                    {{ endforeach; }}
                </label>
            </div>
        </form>
        <div class="absolute top-full right-0 w-full h-px rounded-full max-w-sm bg-gradient-to-r from-transparent from-10% via-purple-500 to-transparent drop-shadow-xl"></div>
    </div>
</div>
{{ endBlock () }}

# aura-app

Aura.PHP application

## Library

### Backend

* Template Engine / [Qiq](https://github.com/qiqphp/qiq)
* Session / [Aura.Session](https://github.com/auraphp/Aura.Session)
* Auth / [Aura.Auth](https://github.com/auraphp/Aura.Auth)
* Accept / [Aura.Accept](https://github.com/auraphp/Aura.Accept)
* Router / [Aura.Router](https://github.com/auraphp/Aura.Router)
* DI / [Aura.Di](https://github.com/auraphp/Aura.Di)
* Env / [Koriym.EnvJson](https://github.com/koriym/Koriym.EnvJson)

### Frontend

* JavaScript bundler / [Rollup](https://rollupjs.org)

### Features

* ID & password login
* Flash message

### Architecture

```mermaid
sequenceDiagram
    Bootstrap ->> RequestDispatcher: Call "__invoke()"
    RequestDispatcher ->> RequestDispatcher: Get "RequestHandler"
    RequestDispatcher ->> CloudflareTurnstileVerificationHandler: Call "__invoke()"
    RequestDispatcher ->> RequestHandler: Call "formValidate()" if using form
    RequestDispatcher ->> AdminAuthenticationHandler: Call "__invoke()"
    RequestDispatcher ->> RequestHandler: Call "onGet()" or "onPost()"
    RequestHandler -->> RequestDispatcher: Return "RequestHandler"
    RequestDispatcher ->> RequestDispatcher: Get "RendererInterface" ("Json" or "HTML" or "TEXT")
    RequestDispatcher ->> RendererInterface: Call "render()"
    RendererInterface -->> RequestDispatcher: Return render result
    RequestDispatcher ->> Response: Get "Response"
    RequestDispatcher ->> RequestDispatcher: Set render result to "Response"
    RequestDispatcher -->> Bootstrap: Return "Response"
```

## Execute on CLI

```bash
composer run cli get /hello
```

<?php

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/auth.php';


/*
|--------------------------------------------------------------------------
| Se já estiver logado, não precisa fazer login novamente
|--------------------------------------------------------------------------
*/

if (usuarioLogado()) {

    if (primeiroAcesso()) {

        header(
            'Location: alterar_senha.php?primeiro=1'
        );

    } else {

        header(
            'Location: index.php'
        );
    }

    exit;
}


$erro = $_GET['err'] ?? '';
$mensagem = $_GET['msg'] ?? '';

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - CRUD Mundo</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body class="login-body">

    <div class="login-container">

        <div class="login-card">

            <h1>CRUD Mundo</h1>

            <p class="login-subtitle">
                Acesse sua conta
            </p>


            <?php if ($erro !== ''): ?>

                <div class="alert alert-error">

                    <?= htmlspecialchars($erro) ?>

                </div>

            <?php endif; ?>


            <?php if ($mensagem !== ''): ?>

                <div class="alert alert-success">

                    <?= htmlspecialchars($mensagem) ?>

                </div>

            <?php endif; ?>


            <form
                action="actions/login_process.php"
                method="POST"
            >

                <div class="form-group">

                    <label for="email">
                        E-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="150"
                        autocomplete="username"
                        required
                        autofocus
                    >

                </div>


                <div class="form-group">

                    <label for="senha">
                        Senha
                    </label>

                    <div class="password-field">
                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-password"
                            data-toggle-password="senha" aria-label="Mostrar senha"
                            aria-pressed="false" title="Mostrar senha">👁</button>
                    </div>

                </div>


                <button
                    type="submit"
                    class="btn btn-login"
                >
                    Entrar
                </button>

            </form>


            <p class="login-info">

                Após 3 tentativas consecutivas
                com senha incorreta, o acesso será
                bloqueado por 15 minutos.

            </p>

        </div>

    </div>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (botao) {
            botao.addEventListener('click', function () {
                const campo = document.getElementById(botao.dataset.togglePassword);
                const mostrar = campo.type === 'password';
                campo.type = mostrar ? 'text' : 'password';
                botao.setAttribute('aria-pressed', String(mostrar));
                botao.setAttribute('aria-label', mostrar ? 'Ocultar senha' : 'Mostrar senha');
                botao.setAttribute('title', mostrar ? 'Ocultar senha' : 'Mostrar senha');
                botao.textContent = mostrar ? '🙈' : '👁';
            });
        });
    </script>
</body>

</html>
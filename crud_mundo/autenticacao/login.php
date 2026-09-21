<?php

require_once 'config/conexao.php';
require_once 'config/auth.php';


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

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        autocomplete="current-password"
                        required
                    >

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

</body>

</html>
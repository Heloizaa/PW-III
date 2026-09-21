<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| Verifica se existe usuário logado
|--------------------------------------------------------------------------
*/

function usuarioLogado(): bool
{
    return isset(
        $_SESSION['usuario_id'],
        $_SESSION['usuario_tipo']
    );
}


/*
|--------------------------------------------------------------------------
| Verifica se é primeiro acesso
|--------------------------------------------------------------------------
*/

function primeiroAcesso(): bool
{
    return usuarioLogado()
        && !empty($_SESSION['primeiro_acesso']);
}


/*
|--------------------------------------------------------------------------
| Exige login
|--------------------------------------------------------------------------
*/

function exigirLogin(): void
{
    if (!usuarioLogado()) {

        header(
            'Location: login.php?err=' .
            urlencode('Faça login para acessar o sistema.')
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Exige acesso ao sistema
|--------------------------------------------------------------------------
|
| Se for o primeiro acesso, o usuário só poderá
| acessar alterar_senha.php.
|
*/

function exigirAcessoSistema(): void
{
    exigirLogin();

    if (primeiroAcesso()) {

        $pagina = basename(
            $_SERVER['SCRIPT_FILENAME'] ?? ''
        );

        if ($pagina !== 'alterar_senha.php') {

            $diretorio = basename(
                dirname($_SERVER['SCRIPT_FILENAME'] ?? '')
            );

            if ($diretorio === 'actions') {

                header(
                    'Location: ../alterar_senha.php?primeiro=1'
                );

            } else {

                header(
                    'Location: alterar_senha.php?primeiro=1'
                );
            }

            exit;
        }
    }
}


/*
|--------------------------------------------------------------------------
| Verifica administrador
|--------------------------------------------------------------------------
*/

function ehAdministrador(): bool
{
    return usuarioLogado()
        && $_SESSION['usuario_tipo'] === 'administrador';
}


/*
|--------------------------------------------------------------------------
| Exige administrador
|--------------------------------------------------------------------------
*/

function exigirAdministrador(): void
{
    exigirAcessoSistema();

    if (!ehAdministrador()) {

        http_response_code(403);

        ?>

        <!DOCTYPE html>

        <html lang="pt-br">

        <head>

            <meta charset="UTF-8">

            <meta
                name="viewport"
                content="width=device-width, initial-scale=1.0"
            >

            <title>Acesso negado</title>

            <link
                rel="stylesheet"
                href="../css/style.css"
            >

        </head>

        <body>

            <div class="container">

                <div class="alert alert-error">

                    <strong>Acesso negado.</strong>

                    <br><br>

                    Você não possui permissão
                    para realizar esta ação.

                </div>

                <a
                    href="../index.php"
                    class="btn btn-edit"
                >
                    Voltar para consultas
                </a>

            </div>

        </body>

        </html>

        <?php

        exit;
    }
}
?>
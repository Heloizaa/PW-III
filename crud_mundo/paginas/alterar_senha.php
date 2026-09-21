<?php

require_once 'config/conexao.php';
require_once 'config/auth.php';

exigirLogin();


$erro = '';
$sucesso = '';

$primeiro = primeiroAcesso();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $senhaAtual =
        $_POST['senha_atual'] ?? '';

    $novaSenha =
        $_POST['nova_senha'] ?? '';

    $confirmacao =
        $_POST['confirmacao'] ?? '';


    /*
    |--------------------------------------------------------------------------
    | Validações
    |--------------------------------------------------------------------------
    */

    if (
        $senhaAtual === ''
        ||
        $novaSenha === ''
        ||
        $confirmacao === ''
    ) {

        $erro =
            'Preencha todos os campos.';

    } elseif (strlen($novaSenha) < 6) {

        $erro =
            'A nova senha deve ter pelo menos 6 caracteres.';

    } elseif ($novaSenha === $senhaAtual) {

        $erro =
            'A nova senha deve ser diferente da senha atual.';

    } elseif ($novaSenha !== $confirmacao) {

        $erro =
            'A confirmação da senha não confere.';

    } else {

        /*
        |--------------------------------------------------------------------------
        | Busca senha atual
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            SELECT senha
            FROM USUARIOS
            WHERE id = ?
        ");

        $stmt->execute([
            $_SESSION['usuario_id']
        ]);

        $usuario = $stmt->fetch();


        if (
            !$usuario
            ||
            !password_verify(
                $senhaAtual,
                $usuario['senha']
            )
        ) {

            $erro =
                'A senha atual está incorreta.';

        } else {

            /*
            |--------------------------------------------------------------------------
            | Cria hash da nova senha
            |--------------------------------------------------------------------------
            */

            $novoHash =
                password_hash(
                    $novaSenha,
                    PASSWORD_DEFAULT
                );


            /*
            |--------------------------------------------------------------------------
            | Atualiza senha
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                UPDATE USUARIOS
                SET
                    senha = ?,
                    primeiro_acesso = 0,
                    tentativas_login = 0,
                    bloqueado_ate = NULL
                WHERE id = ?
            ");

            $stmt->execute([
                $novoHash,
                $_SESSION['usuario_id']
            ]);


            /*
            |--------------------------------------------------------------------------
            | Registra LOG
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                INSERT INTO LOGS
                (
                    id_usuario,
                    acao,
                    descricao,
                    ip
                )
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $_SESSION['usuario_id'],
                'ALTERACAO_SENHA',
                $primeiro
                    ? 'Senha alterada no primeiro acesso.'
                    : 'Senha alterada pelo usuário.',
                $_SERVER['REMOTE_ADDR'] ?? null
            ]);


            /*
            |--------------------------------------------------------------------------
            | Atualiza sessão
            |--------------------------------------------------------------------------
            */

            $_SESSION['primeiro_acesso'] = false;

            $primeiro = false;

            $sucesso =
                'Senha alterada com sucesso!';
        }
    }
}

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= $primeiro
            ? 'Primeiro acesso'
            : 'Alterar senha'
        ?>
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="container small-container">

    <h2>

        <?= $primeiro
            ? 'Primeiro acesso'
            : 'Alterar senha'
        ?>

    </h2>


    <?php if ($primeiro): ?>

        <div class="alert alert-error">

            <strong>Atenção!</strong>

            <br><br>

            Este é o seu primeiro acesso.

            <br>

            Por segurança, você precisa
            trocar a senha temporária
            antes de utilizar o sistema.

        </div>

    <?php endif; ?>


    <?php if ($erro): ?>

        <div class="alert alert-error">

            <?= htmlspecialchars($erro) ?>

        </div>

    <?php endif; ?>


    <?php if ($sucesso): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars($sucesso) ?>

            <br><br>

            <a
                href="index.php"
                class="btn btn-edit"
            >
                Continuar para o sistema
            </a>

        </div>

    <?php endif; ?>


    <?php if (!$sucesso): ?>

        <form
            method="POST"
        >

            <div class="form-group">

                <label for="senha_atual">
                    Senha atual
                </label>

                <input
                    type="password"
                    id="senha_atual"
                    name="senha_atual"
                    required
                    autocomplete="current-password"
                >

            </div>


            <div class="form-group">

                <label for="nova_senha">
                    Nova senha
                </label>

                <input
                    type="password"
                    id="nova_senha"
                    name="nova_senha"
                    minlength="6"
                    required
                    autocomplete="new-password"
                >

            </div>


            <div class="form-group">

                <label for="confirmacao">
                    Confirmar nova senha
                </label>

                <input
                    type="password"
                    id="confirmacao"
                    name="confirmacao"
                    minlength="6"
                    required
                    autocomplete="new-password"
                >

            </div>


            <button
                type="submit"
                class="btn btn-edit"
            >
                Salvar nova senha
            </button>


            <?php if (!$primeiro): ?>

                <a
                    href="index.php"
                    class="btn btn-cancel"
                >
                    Voltar
                </a>

            <?php endif; ?>

        </form>

    <?php endif; ?>

</div>

</body>

</html>
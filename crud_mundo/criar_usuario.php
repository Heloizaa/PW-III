<?php

require_once 'config/conexao.php';
require_once 'config/auth.php';

exigirAdministrador();


$erro = '';
$sucesso = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome =
        trim($_POST['nome'] ?? '');

    $email =
        trim($_POST['email'] ?? '');

    $senha =
        $_POST['senha'] ?? '';

    $tipo =
        $_POST['tipo'] ?? 'usuario';


    /*
    |--------------------------------------------------------------------------
    | Validações
    |--------------------------------------------------------------------------
    */

    if (
        $nome === ''
        ||
        $email === ''
        ||
        $senha === ''
    ) {

        $erro =
            'Preencha todos os campos.';

    } elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $erro =
            'Informe um e-mail válido.';

    } elseif (strlen($senha) < 6) {

        $erro =
            'A senha deve ter pelo menos 6 caracteres.';

    } elseif (
        !in_array(
            $tipo,
            ['usuario', 'administrador'],
            true
        )
    ) {

        $erro =
            'Tipo de usuário inválido.';

    } else {

        try {

            /*
            |--------------------------------------------------------------------------
            | Cria senha criptografada
            |--------------------------------------------------------------------------
            */

            $senhaHash =
                password_hash(
                    $senha,
                    PASSWORD_DEFAULT
                );


            /*
            |--------------------------------------------------------------------------
            | Cadastra usuário
            |--------------------------------------------------------------------------
            |
            | primeiro_acesso = 1
            | significa que ele será obrigado
            | a trocar a senha.
            |
            */

            $stmt = $pdo->prepare("
                INSERT INTO USUARIOS
                (
                    nome,
                    email,
                    senha,
                    tipo,
                    primeiro_acesso
                )
                VALUES (?, ?, ?, ?, 1)
            ");

            $stmt->execute([
                $nome,
                $email,
                $senhaHash,
                $tipo
            ]);


            /*
            |--------------------------------------------------------------------------
            | LOG
            |--------------------------------------------------------------------------
            */

            $log = $pdo->prepare("
                INSERT INTO LOGS
                (
                    id_usuario,
                    acao,
                    descricao,
                    ip
                )
                VALUES (?, ?, ?, ?)
            ");

            $log->execute([
                $_SESSION['usuario_id'],
                'CRIACAO_USUARIO',
                'Novo usuário criado: ' . $email,
                $_SERVER['REMOTE_ADDR'] ?? null
            ]);


            $sucesso =
                'Usuário cadastrado com sucesso.';


        } catch (PDOException $e) {

            if ($e->getCode() === '23000') {

                $erro =
                    'Esse e-mail já está cadastrado.';

            } else {

                $erro =
                    'Não foi possível cadastrar o usuário.';
            }
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

    <title>Cadastrar Usuário</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>

<div class="container small-container">

    <h2>
        Cadastrar Usuário
    </h2>


    <?php if ($erro): ?>

        <div class="alert alert-error">

            <?= htmlspecialchars($erro) ?>

        </div>

    <?php endif; ?>


    <?php if ($sucesso): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars($sucesso) ?>

        </div>

    <?php endif; ?>


    <form method="POST">

        <div class="form-group">

            <label for="nome">
                Nome
            </label>

            <input
                type="text"
                id="nome"
                name="nome"
                required
            >

        </div>


        <div class="form-group">

            <label for="email">
                E-mail
            </label>

            <input
                type="email"
                id="email"
                name="email"
                required
            >

        </div>


        <div class="form-group">

            <label for="senha">
                Senha temporária
            </label>

            <input
                type="password"
                id="senha"
                name="senha"
                minlength="6"
                required
            >

        </div>


        <div class="form-group">

            <label for="tipo">
                Tipo de acesso
            </label>

            <select
                id="tipo"
                name="tipo"
                required
            >

                <option value="usuario">
                    Usuário — somente consultas
                </option>

                <option value="administrador">
                    Administrador — acesso completo
                </option>

            </select>

        </div>


        <button
            type="submit"
            class="btn btn-add"
        >
            Cadastrar
        </button>


        <a
            href="index.php"
            class="btn btn-cancel"
        >
            Voltar
        </a>

    </form>

</div>

</body>

</html>
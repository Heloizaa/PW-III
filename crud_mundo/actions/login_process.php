<?php

require_once '../config/conexao.php';
require_once '../config/auth.php';


/*
|--------------------------------------------------------------------------
| Garante que o formulário foi enviado por POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header(
        'Location: ../login.php'
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Dados enviados pelo formulário
|--------------------------------------------------------------------------
*/

$email = trim(
    $_POST['email'] ?? ''
);

$senha =
    $_POST['senha'] ?? '';


/*
|--------------------------------------------------------------------------
| Verifica se os campos foram preenchidos
|--------------------------------------------------------------------------
*/

if (
    $email === ''
    ||
    $senha === ''
) {

    header(
        'Location: ../login.php?err=' .
        urlencode(
            'Preencha o e-mail e a senha.'
        )
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Busca o usuário
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        nome,
        email,
        senha,
        tipo,
        tentativas_login,
        bloqueado_ate,
        primeiro_acesso

    FROM USUARIOS

    WHERE email = ?

    LIMIT 1
");

$stmt->execute([
    $email
]);

$usuario = $stmt->fetch();


/*
|--------------------------------------------------------------------------
| Usuário não encontrado
|--------------------------------------------------------------------------
*/

if (!$usuario) {

    header(
        'Location: ../login.php?err=' .
        urlencode(
            'E-mail ou senha incorretos.'
        )
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Verifica se o usuário está bloqueado
|--------------------------------------------------------------------------
*/

if (
    !empty($usuario['bloqueado_ate'])
    &&
    strtotime($usuario['bloqueado_ate']) > time()
) {

    $tempoRestante =
        strtotime(
            $usuario['bloqueado_ate']
        ) - time();

    $minutos =
        ceil(
            $tempoRestante / 60
        );


    header(
        'Location: ../login.php?err=' .
        urlencode(
            "Acesso bloqueado. Tente novamente em aproximadamente {$minutos} minuto(s)."
        )
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Se o bloqueio já terminou, limpa o bloqueio
|--------------------------------------------------------------------------
*/

if (
    !empty($usuario['bloqueado_ate'])
    &&
    strtotime($usuario['bloqueado_ate']) <= time()
) {

    $stmt = $pdo->prepare("
        UPDATE USUARIOS

        SET
            tentativas_login = 0,
            bloqueado_ate = NULL

        WHERE id = ?
    ");

    $stmt->execute([
        $usuario['id']
    ]);

    $usuario['tentativas_login'] = 0;
    $usuario['bloqueado_ate'] = null;
}


/*
|--------------------------------------------------------------------------
| VERIFICA A SENHA
|--------------------------------------------------------------------------
*/

if (
    !password_verify(
        $senha,
        $usuario['senha']
    )
) {

    /*
    |--------------------------------------------------------------------------
    | Soma uma tentativa
    |--------------------------------------------------------------------------
    */

    $tentativas =
        (int)$usuario['tentativas_login'] + 1;


    /*
    |--------------------------------------------------------------------------
    | 3 tentativas erradas = bloqueio de 15 minutos
    |--------------------------------------------------------------------------
    */

    if ($tentativas >= 3) {

        $bloqueadoAte =
            date(
                'Y-m-d H:i:s',
                time() + (15 * 60)
            );


        $stmt = $pdo->prepare("
            UPDATE USUARIOS

            SET
                tentativas_login = 0,
                bloqueado_ate = ?

            WHERE id = ?
        ");

        $stmt->execute([
            $bloqueadoAte,
            $usuario['id']
        ]);


        /*
        |--------------------------------------------------------------------------
        | LOG DO BLOQUEIO
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
            $usuario['id'],
            'LOGIN_BLOQUEADO',
            'Usuário bloqueado após 3 tentativas de login incorretas.',
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);


        header(
            'Location: ../login.php?err=' .
            urlencode(
                'Acesso bloqueado por 15 minutos devido a 3 tentativas de senha incorretas.'
            )
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Atualiza número de tentativas
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE USUARIOS

        SET tentativas_login = ?

        WHERE id = ?
    ");

    $stmt->execute([
        $tentativas,
        $usuario['id']
    ]);


    /*
    |--------------------------------------------------------------------------
    | LOG DA TENTATIVA INCORRETA
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
        $usuario['id'],
        'LOGIN_FALHA',
        'Tentativa de login com senha incorreta.',
        $_SERVER['REMOTE_ADDR'] ?? null
    ]);


    $tentativasRestantes =
        3 - $tentativas;


    header(
        'Location: ../login.php?err=' .
        urlencode(
            "E-mail ou senha incorretos. Tentativas restantes: {$tentativasRestantes}."
        )
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| LOGIN CORRETO
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Zera tentativas de login
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    UPDATE USUARIOS

    SET
        tentativas_login = 0,
        bloqueado_ate = NULL

    WHERE id = ?
");

$stmt->execute([
    $usuario['id']
]);


/*
|--------------------------------------------------------------------------
| Cria sessão
|--------------------------------------------------------------------------
*/

session_regenerate_id(true);


$_SESSION['usuario_id'] =
    $usuario['id'];

$_SESSION['usuario_nome'] =
    $usuario['nome'];

$_SESSION['usuario_email'] =
    $usuario['email'];

$_SESSION['usuario_tipo'] =
    $usuario['tipo'];

$_SESSION['primeiro_acesso'] =
    (bool)$usuario['primeiro_acesso'];


/*
|--------------------------------------------------------------------------
| LOG DO LOGIN
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
    $usuario['id'],
    'LOGIN',
    'Usuário realizou login com sucesso.',
    $_SERVER['REMOTE_ADDR'] ?? null
]);


/*
|--------------------------------------------------------------------------
| PRIMEIRO ACESSO
|--------------------------------------------------------------------------
|
| Se for o primeiro acesso, precisa obrigatoriamente
| trocar a senha.
|
*/

if (
    (bool)$usuario['primeiro_acesso']
) {

    header(
        'Location: ../alterar_senha.php?primeiro=1'
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| LOGIN NORMAL
|--------------------------------------------------------------------------
*/

header(
    'Location: ../index.php'
);

exit;

?>
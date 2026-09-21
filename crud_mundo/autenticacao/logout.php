<?php

require_once 'config/conexao.php';
require_once 'config/auth.php';


/*
|--------------------------------------------------------------------------
| Registra logout
|--------------------------------------------------------------------------
*/

if (usuarioLogado()) {

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
        'LOGOUT',
        'Usuário saiu do sistema.',
        $_SERVER['REMOTE_ADDR'] ?? null
    ]);
}


/*
|--------------------------------------------------------------------------
| Destrói sessão
|--------------------------------------------------------------------------
*/

$_SESSION = [];


if (ini_get('session.use_cookies')) {

    $params =
        session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}


session_destroy();


header(
    'Location: login.php?msg=' .
    urlencode(
        'Você saiu do sistema.'
    )
);

exit;

?>
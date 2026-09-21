<?php

// ATIVA EXIBIÇÃO DE ERROS PARA DIAGNÓSTICO
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// CONEXÃO E AUTENTICAÇÃO

require_once 'config/conexao.php';
require_once 'config/auth.php';

exigirAcessoSistema();


$totalCidades = 0;
$totalPaises = 0;

$paises = [];
$cidades = [];


try {

    // CONTAGEM DE CIDADES

    $totalCidades =
        $pdo->query(
            "SELECT COUNT(*) FROM cidades"
        )->fetchColumn() ?: 0;


    // CONTAGEM DE PAÍSES

    $totalPaises =
        $pdo->query(
            "SELECT COUNT(*) FROM paises"
        )->fetchColumn() ?: 0;


    // CONSULTA DOS PAÍSES

    $paises =
        $pdo->query(
            "SELECT
                p.*,
                c.nome AS continente_nome,
                g.nome AS governante_nome

             FROM paises p

             LEFT JOIN continentes c
                ON p.id_continente = c.id

             LEFT JOIN governantes g
                ON p.id_governante = g.id"
        )->fetchAll();


    // CONSULTA DAS CIDADES

    $cidades =
        $pdo->query(
            "SELECT
                c.*,
                p.nome AS pais_nome,
                g.nome AS governante_nome

             FROM cidades c

             LEFT JOIN paises p
                ON c.id_pais = p.id

             LEFT JOIN governantes g
                ON c.id_governante = g.id"
        )->fetchAll();


} catch (\PDOException $e) {

    die(
        "<div style='
            color:red;
            padding:20px;
            background:#fee;
            font-family:sans-serif;
        '>

            <h3>
                Erro na consulta do Banco de Dados:
            </h3>"

            . htmlspecialchars($e->getMessage()) .

        "</div>"
    );
}

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>
        CRUD Mundo
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="container">


    <h1>
        Painel de Controle Geográfico
    </h1>


    <!-- MENSAGEM DE SUCESSO -->

    <?php if (
        isset($_GET['msg'])
        &&
        !empty($_GET['msg'])
    ): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars($_GET['msg']) ?>

        </div>

    <?php endif; ?>


    <!-- MENSAGEM DE ERRO -->

    <?php if (
        isset($_GET['err'])
        &&
        !empty($_GET['err'])
    ): ?>

        <div class="alert alert-error">

            <?= htmlspecialchars($_GET['err']) ?>

        </div>

    <?php endif; ?>


    <!-- ESTATÍSTICAS -->

    <div
        style="
            background: #eef2f3;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        "
    >

        <strong>
            Estatísticas do Sistema:
        </strong>

        Países Cadastrados:
        <?= $totalPaises ?>

        |

        Cidades Cadastradas:
        <?= $totalCidades ?>

    </div>


    <!--
    ==========================================================
    FUNÇÕES EXCLUSIVAS DO ADMINISTRADOR
    ==========================================================
    -->

    <?php if (ehAdministrador()): ?>

        <div class="flex-buttons">

            <a
                href="pais_form.php"
                class="btn btn-add"
            >
                + Cadastrar País
            </a>


            <a
                href="cidade_form.php"
                class="btn btn-city"
            >
                + Cadastrar Cidade
            </a>


            <a
                href="criar_usuario.php"
                class="btn btn-edit"
            >
                + Cadastrar Usuário
            </a>

        </div>

    <?php endif; ?>


    <!--
    ==========================================================
    ALTERAR SENHA
    DISPONÍVEL PARA USUÁRIO E ADMINISTRADOR
    ==========================================================
    -->

    <div class="flex-buttons">

        <a
            href="alterar_senha.php"
            class="btn btn-edit"
        >
            Alterar senha
        </a>

    </div>


    <!-- CAMPO DE PESQUISA -->

    <input
        type="text"
        id="search"
        class="search-box"
        placeholder="Busca dinâmica em tempo real (digite o nome de um país ou cidade)..."
    >


    <!--
    ==========================================================
    PAÍSES
    ==========================================================
    -->

    <h2>
        Países
    </h2>


    <table>

        <thead>

            <tr>

                <th>
                    Nome
                </th>

                <th>
                    Continente
                </th>

                <th>
                    População
                </th>

                <th>
                    Área (km²)
                </th>

                <th>
                    Moeda
                </th>

                <th>
                    Governante
                </th>


                <?php if (ehAdministrador()): ?>

                    <th>
                        Ações
                    </th>

                <?php endif; ?>

            </tr>

        </thead>


        <tbody class="dados-tabela">


            <?php if (empty($paises)): ?>

                <tr>

                    <td
                        colspan="<?= ehAdministrador() ? 7 : 6 ?>"
                        style="
                            text-align:center;
                            color:#7f8c8d;
                        "
                    >

                        Nenhum país cadastrado ainda.

                    </td>

                </tr>

            <?php endif; ?>


            <?php foreach ($paises as $p): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars(
                            $p['nome']
                        ) ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $p['continente_nome']
                            ?? 'Não associado'
                        ) ?>
                    </td>


                    <td>
                        <?= number_format(
                            $p['populacao'],
                            0,
                            ',',
                            '.'
                        ) ?>
                    </td>


                    <td>
                        <?= number_format(
                            $p['area_km2'],
                            2,
                            ',',
                            '.'
                        ) ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $p['moeda']
                        ) ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $p['governante_nome']
                            ?? 'Nenhum'
                        ) ?>
                    </td>


                    <!-- AÇÕES SOMENTE DO ADMINISTRADOR -->

                    <?php if (ehAdministrador()): ?>

                        <td>

                            <a
                                href="pais_form.php?id=<?= (int)$p['id'] ?>"
                                class="btn btn-edit"
                            >
                                Editar
                            </a>


                            <a
                                href="actions/pais_process.php?action=delete&id=<?= (int)$p['id'] ?>"
                                class="btn btn-delete"
                                onclick="confirmarExclusao(event)"
                            >
                                Excluir
                            </a>

                        </td>

                    <?php endif; ?>

                </tr>

            <?php endforeach; ?>


        </tbody>

    </table>


    <!--
    ==========================================================
    CIDADES
    ==========================================================
    -->

    <h2>
        Cidades
    </h2>


    <table>

        <thead>

            <tr>

                <th>
                    Cidade
                </th>

                <th>
                    País Pertencente
                </th>

                <th>
                    População
                </th>

                <th>
                    Clima
                </th>

                <th>
                    Governante
                </th>


                <?php if (ehAdministrador()): ?>

                    <th>
                        Ações
                    </th>

                <?php endif; ?>

            </tr>

        </thead>


        <tbody class="dados-tabela">


            <?php if (empty($cidades)): ?>

                <tr>

                    <td
                        colspan="<?= ehAdministrador() ? 6 : 5 ?>"
                        style="
                            text-align:center;
                            color:#7f8c8d;
                        "
                    >

                        Nenhuma cidade cadastrada ainda.

                    </td>

                </tr>

            <?php endif; ?>


            <?php foreach ($cidades as $c): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars(
                            $c['nome']
                        ) ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $c['pais_nome']
                            ?? 'Desconhecido'
                        ) ?>
                    </td>


                    <td>
                        <?= number_format(
                            $c['populacao'],
                            0,
                            ',',
                            '.'
                        ) ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $c['clima']
                        ) ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $c['governante_nome']
                            ?? 'Nenhum'
                        ) ?>
                    </td>


                    <!-- AÇÕES SOMENTE DO ADMINISTRADOR -->

                    <?php if (ehAdministrador()): ?>

                        <td>

                            <a
                                href="cidade_form.php?id=<?= (int)$c['id'] ?>"
                                class="btn btn-edit"
                            >
                                Editar
                            </a>


                            <a
                                href="actions/cidade_process.php?action=delete&id=<?= (int)$c['id'] ?>"
                                class="btn btn-delete"
                                onclick="confirmarExclusao(event)"
                            >
                                Excluir
                            </a>

                        </td>

                    <?php endif; ?>

                </tr>

            <?php endforeach; ?>


        </tbody>

    </table>


</div>


<script src="js/main.js"></script>

</body>

</html>
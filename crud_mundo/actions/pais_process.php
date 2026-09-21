<?php
// Ativa a exibição de erros para diagnóstico, caso falte algum campo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Volta um nível para encontrar a conexão

require_once '../config/conexao.php';
require_once '../config/auth.php';

exigirAdministrador();
// Operação de Exclusão (DELETE)
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM paises WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: ../index.php?msg=País deletado com sucesso!");
    } catch (\PDOException $e) {
        // Se houver cidades vinculadas e o banco restringir, cai aqui
        header("Location: ../index.php?err=Não é possível deletar o país. Certifique-se de que não existem cidades associadas a ele.");
    }
    exit;
}

// Operação de Cadastro (CREATE) ou Edição (UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $nome = $_POST['nome'];
    $id_continente = $_POST['id_continente'] ?: null;
    $populacao = $_POST['populacao'] ?: 0;
    $area_km2 = $_POST['area_km2'] ?: 0;
    $idioma = $_POST['idioma'];
    $id_governante = $_POST['id_governante'] ?: null;
    $clima = $_POST['clima'];
    $regime_politico = $_POST['regime_politico'];
    $moeda = $_POST['moeda'];

    if (empty($nome)) {
        die("O campo Nome do País é obrigatório.");
    }

    if ($id) {
        // UPDATE - Atualizar registro existente
        $sql = "UPDATE paises SET nome=?, id_continente=?, populacao=?, area_km2=?, idioma=?, id_governante=?, clima=?, regime_politico=?, moeda=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $id_continente, $populacao, $area_km2, $idioma, $id_governante, $clima, $regime_politico, $moeda, $id]);
    } else {
        // INSERT - Criar novo registro
        $sql = "INSERT INTO paises (nome, id_continente, populacao, area_km2, idioma, id_governante, clima, regime_politico, moeda) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $id_continente, $populacao, $area_km2, $idioma, $id_governante, $clima, $regime_politico, $moeda]);
    }
    
    header("Location: ../index.php?msg=País salvo com sucesso!");
    exit;
}
?>
<?php
// 1. ATIVA EXIBIÇÃO DE ERROS PARA NÃO FICAR TELA BRANCA SE ALGO FALHAR
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. CORREÇÃO DO CAMINHO: Adicionado "../" para sair da pasta actions e achar a config

require_once '../config/conexao.php';
require_once '../config/auth.php';

exigirAdministrador();
// Operação de Exclusão (DELETE)
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM cidades WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: ../index.php?msg=Cidade deletada com sucesso!");
    } catch (\PDOException $e) {
        header("Location: ../index.php?err=" . urlencode("Erro ao deletar cidade: " . $e->getMessage()));
    }
    exit;
}

// Operação de Cadastro (CREATE) ou Edição (UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $nome = $_POST['nome'];
    $id_pais = $_POST['id_pais'];
    $populacao = $_POST['populacao'] ?: 0;
    $area_km2 = $_POST['area_km2'] ?: 0;
    $clima = $_POST['clima'];
    $id_governante = $_POST['id_governante'] ?: null;
    $data_fundacao = $_POST['data_fundacao'] ?: null;

    // Validação de segurança básica antes de enviar ao banco
    if (empty($nome) || empty($id_pais)) {
        die("Os campos Nome da Cidade e País Pertencente são obrigatórios.");
    }

    try {
        if ($id) {
            // UPDATE - Atualizar registro existente
            $sql = "UPDATE cidades SET nome=?, id_pais=?, populacao=?, area_km2=?, clima=?, id_governante=?, data_fundacao=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $id_pais, $populacao, $area_km2, $clima, $id_governante, $data_fundacao, $id]);
        } else {
            // INSERT - Criar novo registro
            $sql = "INSERT INTO cidades (nome, id_pais, populacao, area_km2, clima, id_governante, data_fundacao) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $id_pais, $populacao, $area_km2, $clima, $id_governante, $data_fundacao]);
        }
        
        header("Location: ../index.php?msg=Cidade salva com sucesso!");
        exit;

    } catch (\PDOException $e) {
        // Se o banco rejeitar por erro de coluna, ele para aqui e avisa
        die("<div style='color:red; padding:20px; background:#fee; font-family:sans-serif;'>
                <h3>Erro ao salvar no Banco de Dados:</h3>" . $e->getMessage() . "
             </div>");
    }
}
?>
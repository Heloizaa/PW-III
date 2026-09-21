<?php
// 1. ATIVA EXIBIÇÃO DE ERROS PARA DIAGNÓSTICO
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. CONECTA AO BANCO (Como está na raiz, não usa ../)

require_once 'config/conexao.php';
require_once 'config/auth.php';

exigirAdministrador();

$id = $_GET['id'] ?? null;
$cidade = null;

try {
    // Se for uma edição, busca os dados atuais da cidade
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM cidades WHERE id = ?");
        $stmt->execute([$id]);
        $cidade = $stmt->fetch();
    }

    // Busca a lista de países e governantes para os selects do formulário
    $paises = $pdo->query("SELECT id, nome FROM paises ORDER BY nome ASC")->fetchAll();
    $governantes = $pdo->query("SELECT id, nome FROM governantes ORDER BY nome ASC")->fetchAll();

} catch (\PDOException $e) {
    die("Erro ao carregar dados para o formulário: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Formulário de Cidade</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2><?= $id ? 'Editar' : 'Cadastrar' ?> Cidade</h2>
    
    <form action="actions/cidade_process.php" method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">
        
        <div class="form-group">
            <label>Nome da Cidade *</label>
            <input type="text" name="nome" value="<?= $cidade['nome'] ?? '' ?>" required>
        </div>
        
        <div class="form-group">
            <label>País Pertencente *</label>
            <select name="id_pais" required>
                <option value="">Selecione um país...</option>
                <?php foreach($paises as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= isset($cidade['id_pais']) && $cidade['id_pais'] == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>População</label>
            <input type="number" name="populacao" value="<?= $cidade['populacao'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label>Área (km²)</label>
            <input type="number" step="0.01" name="area_km2" value="<?= $cidade['area_km2'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label>Clima</label>
            <input type="text" name="clima" value="<?= $cidade['clima'] ?? '' ?>">
        </div>
        
        <div class="form-group">
            <label>Governante Atual</label>
            <select name="id_governante">
                <option value="">Selecione...</option>
                <?php foreach($governantes as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= isset($cidade['id_governante']) && $cidade['id_governante'] == $g['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Data de Fundação</label>
            <input type="date" name="data_fundacao" value="<?= $cidade['data_fundacao'] ?? '' ?>">
        </div>

        <button type="submit" class="btn btn-city">Salvar</button>
        <a href="index.php" class="btn" style="background-color: #7f8c8d;">Cancelar</a>
    </form>
</div>
</body>
</html>
<?php

require_once 'config/conexao.php';
require_once 'config/auth.php';

exigirAcessoSistema();

$id = $_GET['id'] ?? null;
$pais = $id ? $pdo->prepare("SELECT * FROM paises WHERE id = ?") : null;
if ($pais) { $pais->execute([$id]); $pais = $pais->fetch(); }

$continentes = $pdo->query("SELECT * FROM continentes")->fetchAll();
$governantes = $pdo->query("SELECT * FROM governantes")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Formulário de País</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <h2><?= $id ? 'Editar' : 'Cadastrar' ?> País</h2>
    <form action="actions/pais_process.php" method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">
        
        <div class="form-group"><label>Nome do País *</label><input type="text" name="nome" value="<?= $pais['nome'] ?? '' ?>" required></div>
        
        <div class="form-group">
            <label>Continente</label>
            <select name="id_continente">
                <option value="">Selecione...</option>
                <?php foreach($continentes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= isset($pais['id_continente']) && $pais['id_continente'] == $c['id'] ? 'selected' : '' ?>><?= $c['nome'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group"><label>População</label><input type="number" name="populacao" value="<?= $pais['populacao'] ?? '' ?>"></div>
        <div class="form-group"><label>Área (km²)</label><input type="number" step="0.01" name="area_km2" value="<?= $pais['area_km2'] ?? '' ?>"></div>
        <div class="form-group"><label>Idioma Oficial</label><input type="text" name="idioma" value="<?= $pais['idioma'] ?? '' ?>"></div>
        
        <div class="form-group">
            <label>Governante Atual</label>
            <select name="id_governante">
                <option value="">Selecione...</option>
                <?php foreach($governantes as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= isset($pais['id_governante']) && $pais['id_governante'] == $g['id'] ? 'selected' : '' ?>><?= $g['nome'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group"><label>Clima</label><input type="text" name="clima" value="<?= $pais['clima'] ?? '' ?>"></div>
        <div class="form-group"><label>Regime Político</label><input type="text" name="regime_politico" value="<?= $pais['regime_politico'] ?? '' ?>"></div>
        <div class="form-group"><label>Moeda</label><input type="text" name="moeda" value="<?= $pais['moeda'] ?? '' ?>"></div>

        <button type="submit" class="btn btn-add">Salvar</button>
        <a href="index.php" class="btn" style="background-color: #7f8c8d;">Cancelar</a>
    </form>
</div>
</body>
</html>
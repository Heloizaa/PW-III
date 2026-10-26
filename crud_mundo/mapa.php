<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/config/auth.php';

exigirAcessoSistema();

try {
    $stmt = $pdo->query("SELECT c.id, c.nome AS cidade_nome, p.nome AS pais_nome
                         FROM cidades c
                         INNER JOIN paises p ON p.id = c.id_pais
                         ORDER BY c.nome ASC, p.nome ASC");
    $cidades = $stmt->fetchAll();
} catch (PDOException $e) {
    http_response_code(500);
    exit('Não foi possível carregar as cidades cadastradas. Verifique a conexão com o banco de dados.');
}

$idSelecionado = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$cidadeSelecionada = null;

foreach ($cidades as $item) {
    if ($idSelecionado && (int)$item['id'] === $idSelecionado) {
        $cidadeSelecionada = $item;
        break;
    }
}

if (!$cidadeSelecionada && count($cidades) > 0) {
    $cidadeSelecionada = $cidades[0];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Mundo - Mapa das cidades</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    >
    <style>
        .map-page { max-width: 1100px; margin: 28px auto; padding: 24px; }
        .map-toolbar { display:flex; gap:12px; align-items:end; flex-wrap:wrap; margin:20px 0; }
        .map-toolbar .form-group { flex:1; min-width:230px; margin:0; }
        #mapa { width:100%; height:480px; border-radius:12px; background:#e9eef2; z-index:1; }
        .map-note { margin:12px 0; color:#536273; }
        .map-error { color:#a12626; background:#fff0f0; border:1px solid #f1baba; border-radius:8px; padding:12px; }
        .map-credit { font-size:12px; color:#536273; margin-top:8px; }
    </style>
</head>
<body>
<div class="container map-page">
    <h1>Mapa das cidades</h1>
    <p>Selecione uma cidade cadastrada no CRUD Mundo para visualizar sua localização no mapa.</p>

    <?php if (empty($cidades)): ?>
        <p class="map-note">Ainda não há cidades cadastradas. Cadastre uma cidade antes de abrir o mapa.</p>
    <?php else: ?>
        <form class="map-toolbar" method="get" action="mapa.php">
            <div class="form-group">
                <label for="id">Cidade cadastrada</label>
                <select id="id" name="id" required>
                    <?php foreach ($cidades as $item): ?>
                        <option value="<?= (int)$item['id'] ?>" <?= $cidadeSelecionada && (int)$cidadeSelecionada['id'] === (int)$item['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($item['cidade_nome'] . ' — ' . $item['pais_nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-city">Mostrar no mapa</button>
        </form>

        <h2 id="titulo-localizacao">
            Localização: <?= htmlspecialchars($cidadeSelecionada['cidade_nome'] . ', ' . $cidadeSelecionada['pais_nome'], ENT_QUOTES, 'UTF-8') ?>
        </h2>
        <div id="mensagem-mapa" class="map-note" role="status">Procurando a localização da cidade…</div>
        <div id="mapa" aria-label="Mapa interativo com a localização da cidade selecionada"></div>
        <p class="map-credit">Mapa © OpenStreetMap contributors. Geocodificação fornecida pelo serviço Nominatim.</p>
    <?php endif; ?>

    <p style="margin-top:20px"><a href="index.php" class="btn" style="background:#7f8c8d">Voltar ao painel</a></p>
</div>

<?php if (!empty($cidades) && $cidadeSelecionada): ?>
<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""
></script>
<script>
const localPesquisado = <?= json_encode(
    $cidadeSelecionada['cidade_nome'] . ', ' . $cidadeSelecionada['pais_nome'],
    JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) ?>;

const mapa = L.map('mapa').setView([0, 0], 2);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>'
}).addTo(mapa);

async function localizarCidade() {
    const mensagem = document.getElementById('mensagem-mapa');

    try {
        const url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q='
            + encodeURIComponent(localPesquisado);

        const resposta = await fetch(url, {
            headers: { 'Accept': 'application/json' }
        });

        if (!resposta.ok) {
            throw new Error('O serviço de localização está indisponível no momento.');
        }

        const resultados = await resposta.json();

        if (!resultados.length) {
            mensagem.className = 'map-error';
            mensagem.textContent = 'Não foi possível encontrar "' + localPesquisado + '". Confira se o nome da cidade e do país estão corretos.';
            return;
        }

        const latitude = Number(resultados[0].lat);
        const longitude = Number(resultados[0].lon);

        mapa.setView([latitude, longitude], 12);
        L.marker([latitude, longitude])
            .addTo(mapa)
            .bindPopup(localPesquisado)
            .openPopup();

        mensagem.textContent = 'Localização encontrada para ' + localPesquisado + '.';
    } catch (erro) {
        mensagem.className = 'map-error';
        mensagem.textContent = 'Não foi possível carregar a localização. Verifique sua conexão com a internet e tente novamente.';
    }
}

localizarCidade();
</script>
<?php endif; ?>
</body>
</html>

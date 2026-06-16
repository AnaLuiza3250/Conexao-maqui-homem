<?php
session_start();
// Impede o acesso de usuários que não estão logados
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Home — Controle de Luzes</title>
    <meta name="description"
        content="Painel de controle residencial inteligente para automação de luzes via Arduino na rede local.">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header class="app-header" style="position: relative;">
        
        <a href="logout.php" class="btn-sair" style="
            position: fixed; 
            left: 15px; 
            top: 15px; 
            z-index: 9999;
            display: flex; 
            align-items: center; 
            gap: 8px; 
            color: #6c6c80; 
            text-decoration: none; 
            font-size: 0.9rem; 
            font-weight: 600;
            background: rgba(255, 255, 255, 0.05);
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        " onmouseover="this.style.color='#ff4a4a'; this.style.borderColor='#ff4a4a'; this.style.background='rgba(255, 74, 74, 0.1)'; this.style.boxShadow='0 0 10px rgba(255, 74, 74, 0.2)';" 
           onmouseout="this.style.color='#6c6c80'; this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.background='rgba(255, 255, 255, 0.05)'; this.style.boxShadow='none';">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            Sair
        </a>

        <h1><span class="icon-title"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></span>Smart Home</h1>
        <p>Painel de controle residencial na rede local</p>
        <div id="status-indicator" class="status-indicator">
            <span class="dot"></span>
            <span class="status-text">Desconectado</span>
        </div>
    </header>

    <div class="master-controls">
        <button id="btn-ligar-todas" class="btn-master btn-ligar" onclick="ligarTodas()">
            Ligar Todas
        </button>
        <button id="btn-desligar-todas" class="btn-master btn-desligar" onclick="desligarTodas()">
            Desligar Todas
        </button>
    </div>

    <div class="app-layout">

        <div class="planta-container">

            <div id="sala" class="comodo sala">
                <span class="comodo-icon"><svg class="energy-icon" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg></span>
                <span class="comodo-nome">Sala de Estar</span>
                <span class="status-luz" id="pct-sala">0%</span>
                <div class="container-slider">
                    <input type="range" id="slider-sala" min="0" max="255" value="0" class="slider-controle"
                        oninput="ajustarSliderPorUsuario('S', this.value, 'sala', 'pct-sala')">
                </div>
            </div>

            <div id="cozinha" class="comodo cozinha btn-comodo" onclick="alternarLuz(this, 'C', 'c')">
                <span class="comodo-icon"><svg class="energy-icon" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg></span>
                <span class="comodo-nome">Cozinha</span>
                <span class="status-luz">Apagada</span>
            </div>

            <div id="banheiro" class="comodo banheiro btn-comodo" onclick="alternarLuz(this, 'B', 'b')">
                <span class="comodo-icon"><svg class="energy-icon" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg></span>
                <span class="comodo-nome">Banheiro</span>
                <span class="status-luz">Apagada</span>
            </div>

            <div id="quarto" class="comodo quarto">
                <span class="comodo-icon"><svg class="energy-icon" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg></span>
                <span class="comodo-nome">Dormitório</span>
                <span class="status-luz" id="pct-quarto">0%</span>
                <div class="container-slider">
                    <input type="range" id="slider-quarto" min="0" max="255" value="0" class="slider-controle"
                        oninput="ajustarSliderPorUsuario('Q', this.value, 'quarto', 'pct-quarto')">
                </div>
            </div>

            <div id="jardim" class="comodo jardim">
                <span class="comodo-icon"><svg class="energy-icon" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg></span>
                <span class="comodo-nome">Jardim</span>
                <span class="status-luz" id="status-jardim">Apagado</span>
            </div>

        </div>

        <aside class="log-panel">
            <div class="log-header">
                <div class="log-header-left">
                    <h2>Log de Ações</h2>
                    <span id="log-count" class="log-count">0</span>
                </div>
                <button class="btn-clear-log" onclick="limparLog()">Limpar</button>
            </div>
            <div id="log-body" class="log-body">
                <div class="log-empty">
                    <span class="log-empty-icon"><svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg></span>
                    <span class="log-empty-text">Nenhuma ação registrada</span>
                </div>
            </div>
        </aside>

    </div>

    <footer class="app-footer">
        Smart Home &mdash; Controle de Energia - Monitoramento de Luz &amp; Rede Local/Internet
    </footer>

    <script src="app.js"></script>
</body>

</html>
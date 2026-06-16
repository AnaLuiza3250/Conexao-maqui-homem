// ===== CONFIGURAÇÃO =====
const IP_REDE = "10.139.26.80"; 
/* Usando "localhost" porque o servidor Python e o painel Web estão no mesmo PC.
  (Se for controlar pelo celular, troque "localhost" pelo IP do seu PC na rede local).
*/

// ===== ESTADO DA APLICAÇÃO =====
let timersHardware = { 'S': null, 'Q': null };
let tempoUltimoEnvio = 0;
let isDraggingSlider = false; // Controle para evitar conflito de arraste

// ===== REFERÊNCIAS DO DOM =====
const logBody = () => document.getElementById('log-body');
const logCount = () => document.getElementById('log-count');
const statusIndicator = () => document.getElementById('status-indicator');

// ===== SISTEMA DE LOG (busca do servidor) =====
function renderizarLog(logEntries) {
    const container = logBody();
    const counter = logCount();

    if (!container) return;

    counter.textContent = logEntries.length;

    if (logEntries.length === 0) {
        container.innerHTML = `
            <div class="log-empty">
                <span class="log-empty-icon">📋</span>
                <span class="log-empty-text">Nenhuma ação registrada</span>
            </div>
        `;
        return;
    }

    container.innerHTML = logEntries.map((entry, i) => `
        <div class="log-item" style="animation-delay: ${i < 1 ? '0ms' : 'none'}">
            <div class="log-item-content">
                <div class="log-item-text">${entry.mensagem}</div>
                <div class="log-item-time">${entry.hora} · disp. ${entry.dispositivo || '?'}</div>
            </div>
        </div>
    `).join('');
}

function sincronizarLog() {
    fetch(`http://${IP_REDE}:8080/log`)
        .then(res => res.json())
        .then(logEntries => {
            renderizarLog(logEntries);
        })
        .catch(() => { }); // Silencia erros de rede
}

function limparLog() {
    fetch(`http://${IP_REDE}:8080/log?limpar=1`)
        .then(() => sincronizarLog())
        .catch(() => { });
}

// ===== COMUNICAÇÃO COM O HARDWARE =====
function enviarComandoHardware(letra, valor = "") {
    fetch(`http://${IP_REDE}:8080/?comando=${letra}${valor}`)
        .then(() => {
            atualizarStatusConexao(true);
            // Atualiza o log logo após enviar o comando
            setTimeout(sincronizarLog, 200);
        })
        .catch(erro => {
            console.error("Erro no comando:", erro);
            atualizarStatusConexao(false);
        });
}

function atualizarStatusConexao(online) {
    const el = statusIndicator();
    if (!el) return;
    if (online) {
        el.classList.add('online');
        el.querySelector('.status-text').textContent = 'Conectado';
    } else {
        el.classList.remove('online');
        el.querySelector('.status-text').textContent = 'Desconectado';
    }
}

// ===== CONTROLE DOS SLIDERS (Sala e Quarto) =====
function ajustarSliderPorUsuario(letraComodo, valor, idElemento, idTextoPct) {
    tempoUltimoEnvio = Date.now();
    valor = parseInt(valor); // Garante que é número
    atualizarVisualSlider(valor, idElemento, idTextoPct);

    clearTimeout(timersHardware[letraComodo]);
    timersHardware[letraComodo] = setTimeout(() => {
        enviarComandoHardware(letraComodo, valor);
    }, 40);
}

function atualizarVisualSlider(valor, idElemento, idTextoPct) {
    const elemento = document.getElementById(idElemento);
    const textoPct = document.getElementById(idTextoPct);
    valor = parseInt(valor); // Garante que é número
    const porcentagem = Math.round((valor / 255) * 100);

    textoPct.innerText = `${porcentagem}%`;
    const fatorOpacidade = valor / 255;

    if (valor > 0) {
        elemento.style.backgroundColor = `rgba(255, 204, 0, ${0.08 + (fatorOpacidade * 0.15)})`;
        elemento.style.borderColor = `rgba(255, 204, 0, ${0.15 + (fatorOpacidade * 0.4)})`;
        elemento.style.boxShadow = `inset 0 0 40px rgba(255, 204, 0, ${fatorOpacidade * 0.12}), 0 0 20px rgba(255, 204, 0, ${fatorOpacidade * 0.08})`;
        elemento.classList.add('aceso-visual');
    } else {
        elemento.style.backgroundColor = '';
        elemento.style.borderColor = '';
        elemento.style.boxShadow = '';
        elemento.classList.remove('aceso-visual');
    }
}

// ===== CONTROLE ON/OFF (Cozinha e Banheiro) =====
function alternarLuz(comodo, letraLigar, letraDesligar) {
    tempoUltimoEnvio = Date.now();
    comodo.classList.toggle('aceso');
    const statusTxt = comodo.querySelector('.status-luz');

    if (comodo.classList.contains('aceso')) {
        statusTxt.innerText = "Acesa";
        enviarComandoHardware(letraLigar);
    } else {
        statusTxt.innerText = "Apagada";
        enviarComandoHardware(letraDesligar);
    }
}

// ===== CONTROLES MASTER =====
function ligarTodas() {
    tempoUltimoEnvio = Date.now();

    // Liga sliders no máximo
    document.getElementById('slider-sala').value = 255;
    document.getElementById('slider-quarto').value = 255;
    atualizarVisualSlider(255, 'sala', 'pct-sala');
    atualizarVisualSlider(255, 'quarto', 'pct-quarto');

    // Liga cozinha e banheiro
    const cz = document.getElementById('cozinha');
    cz.classList.add('aceso');
    cz.querySelector('.status-luz').innerText = "Acesa";

    const bh = document.getElementById('banheiro');
    bh.classList.add('aceso');
    bh.querySelector('.status-luz').innerText = "Acesa";

    // Envia comandos para o hardware
    enviarComandoHardware('S', '255');
    enviarComandoHardware('Q', '255');
    enviarComandoHardware('C');
    enviarComandoHardware('B');
}

function desligarTodas() {
    tempoUltimoEnvio = Date.now();
    enviarComandoHardware('X');

    // Zera sliders
    document.getElementById('slider-sala').value = 0;
    document.getElementById('slider-quarto').value = 0;
    atualizarVisualSlider(0, 'sala', 'pct-sala');
    atualizarVisualSlider(0, 'quarto', 'pct-quarto');

    // Desliga cozinha e banheiro
    document.getElementById('cozinha').classList.remove('aceso');
    document.getElementById('cozinha').querySelector('.status-luz').innerText = "Apagada";
    document.getElementById('banheiro').classList.remove('aceso');
    document.getElementById('banheiro').querySelector('.status-luz').innerText = "Apagada";
}

// ===== SINCRONIZAÇÃO AUTOMÁTICA COM O ARDUINO =====
function sincronizarStatusComArduino() {
    if (Date.now() - tempoUltimoEnvio < 2500) return;
    if (isDraggingSlider) return; // Não sincroniza se o usuário estiver arrastando

    fetch(`http://${IP_REDE}:8080/status`)
        .then(res => res.json())
        .then(dados => {
            if (!dados || Object.keys(dados).length === 0) return;

            atualizarStatusConexao(true);

            // 1. Sincroniza Sala de Estar
            document.getElementById('slider-sala').value = dados.S;
            atualizarVisualSlider(dados.S, 'sala', 'pct-sala');

            // 2. Sincroniza Quarto
            document.getElementById('slider-quarto').value = dados.Q;
            atualizarVisualSlider(dados.Q, 'quarto', 'pct-quarto');

            // 3. Sincroniza Cozinha
            const cz = document.getElementById('cozinha');
            if (dados.C === 1) {
                cz.classList.add('aceso');
                cz.querySelector('.status-luz').innerText = "Acesa";
            } else {
                cz.classList.remove('aceso');
                cz.querySelector('.status-luz').innerText = "Apagada";
            }

            // 4. Sincroniza Banheiro
            const bh = document.getElementById('banheiro');
            if (dados.B === 1) {
                bh.classList.add('aceso');
                bh.querySelector('.status-luz').innerText = "Acesa";
            } else {
                bh.classList.remove('aceso');
                bh.querySelector('.status-luz').innerText = "Apagada";
            }

            // 5. Sincroniza Jardim (automático via LDR)
            const jd = document.getElementById('jardim');
            const txtJd = document.getElementById('status-jardim');
            if (dados.J > 0) {
                jd.classList.add('aceso');
                let pctJardim = Math.round((dados.J / 255) * 100);
                txtJd.innerText = `Aceso (${pctJardim}%)`;
            } else {
                jd.classList.remove('aceso');
                txtJd.innerText = "Apagado";
            }
        })
        .catch(() => {
            atualizarStatusConexao(false);
        });
}

// ===== INICIALIZAÇÃO =====
window.addEventListener('load', () => {
    // Evita conflitos ao segurar o slider
    document.querySelectorAll('.slider-controle').forEach(el => {
        el.addEventListener('mousedown', () => isDraggingSlider = true);
        el.addEventListener('touchstart', () => isDraggingSlider = true, {passive: true});
        el.addEventListener('mouseup', () => isDraggingSlider = false);
        el.addEventListener('touchend', () => isDraggingSlider = false);
    });

    // Sincroniza status do Arduino a cada 2 segundos
    setInterval(sincronizarStatusComArduino, 2000);
    // Sincroniza o log do servidor a cada 2 segundos
    setInterval(sincronizarLog, 2000);
    // Busca o log inicial
    sincronizarLog();
});
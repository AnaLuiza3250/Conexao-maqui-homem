import codecs
from http.server import BaseHTTPRequestHandler, HTTPServer
import urllib.parse
import serial
import json
from datetime import datetime

# --- CONFIGURAÇÃO DA PORTA SERIAL ---
porta_com = 'COM10' 

try:
    # Aumentamos o timeout para evitar leituras incompletas da serial
    arduino = serial.Serial(porta_com, 9600, timeout=0.5)
    print(f"Conectado com sucesso na porta {porta_com}!")
except Exception as e:
    print(f"ERRO: Nao foi possivel conectar na porta {porta_com}. Verifique se a IDE do Arduino esta com o Monitor Serial aberto.")
    print(e)
    exit()

# --- LOG CENTRALIZADO (compartilhado entre todos os dispositivos) ---
log_global = []
MAX_LOG = 80

# Mapeamento de comandos para descrições legíveis
DESCRICAO_COMANDOS = {
    'C': ('Cozinha ligada', ''),
    'c': ('Cozinha desligada', ''),
    'B': ('Banheiro ligado', ''),
    'b': ('Banheiro desligado', ''),
    'X': ('Todas as luzes desligadas', ''),
}

def registrar_log(comando_completo, ip_origem):
    """Registra uma ação no log global do servidor."""
    global log_global

    agora = datetime.now().strftime('%H:%M:%S')
    letra = comando_completo[0] if comando_completo else ''
    valor = comando_completo[1:] if len(comando_completo) > 1 else ''

    # Verifica se é um comando mapeado diretamente
    if comando_completo in DESCRICAO_COMANDOS:
        mensagem, icone = DESCRICAO_COMANDOS[comando_completo]
    elif letra == 'S' and valor:
        pct = round((int(valor) / 255) * 100)
        if int(valor) > 0:
            mensagem = f'Sala de Estar adjusted para {pct}%'
        else:
            mensagem = 'Sala de Estar desligada'
    elif letra == 'Q' and valor:
        pct = round((int(valor) / 255) * 100)
        if int(valor) > 0:
            mensagem = f'Dormitório ajustado para {pct}%'
        else:
            mensagem = 'Dormitório desligado'
    else:
        mensagem = f'Comando: {comando_completo}'

    # Identifica o dispositivo pelo final do IP
    dispositivo = ip_origem.split('.')[-1] if ip_origem else '?'
    
    entry = {
        'mensagem': mensagem,
        'hora': agora,
        'dispositivo': dispositivo
    }

    log_global.insert(0, entry)  # Mais recente no topo
    if len(log_global) > MAX_LOG:
        log_global.pop()

class ServidorWeb(BaseHTTPRequestHandler):
    
    def do_OPTIONS(self):
        """Trata requisições de pré-envio do navegador (CORS) de forma automática."""
        self.send_response(200)
        self.send_header("Access-Control-Allow-Origin", "*")
        self.send_header("Access-Control-Allow-Methods", "GET, OPTIONS, POST")
        self.send_header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type")
        self.end_headers()

    def do_GET(self):
        url_analisada = urllib.parse.urlparse(self.path)
        
        # --- ROTA DE ATUALIZAÇÃO AUTOMÁTICA (MÃO DUPLA) ---
        if url_analisada.path == '/status':
            self.send_response(200)
            self.send_header("Content-type", "application/json")
            self.send_header("Access-Control-Allow-Origin", "*") # Permite acesso externo
            self.end_headers()
            
            # Limpa o lixo da linha serial, envia o pedido e lê a resposta imediata
            arduino.reset_input_buffer()
            arduino.write(b'?\n')
            resposta = arduino.readline().decode('utf-8').strip()
            
            dados = {}
            try:
                # Transforma a string "S:120|Q:0|C:1|B:0|J:255" em um objeto JSON estruturado
                if resposta and '|' in resposta:
                    partes = resposta.split('|')
                    for parte in partes:
                        k, v = parte.split(':')
                        dados[k] = int(v)
            except Exception as e:
                print(f"Erro ao processar dados da Serial: {e}")
                
            self.wfile.write(json.dumps(dados).encode('utf-8'))

        # --- ROTA DO LOG CENTRALIZADO ---
        elif url_analisada.path == '/log':
            self.send_response(200)
            self.send_header("Content-type", "application/json")
            self.send_header("Access-Control-Allow-Origin", "*") # Permite acesso externo
            self.end_headers()

            parametros = urllib.parse.parse_qs(url_analisada.query)

            # Ação de limpar o log
            if 'limpar' in parametros:
                log_global.clear()
                self.wfile.write(json.dumps({'status': 'ok'}).encode('utf-8'))
            else:
                # Retorna os logs.
                self.wfile.write(json.dumps(log_global).encode('utf-8'))
            
        # --- ROTA DOS COMANDOS DA WEB ("/") ---
        else:
            self.send_response(200)
            self.send_header("Content-type", "text/plain")
            self.send_header("Access-Control-Allow-Origin", "*") # Permite acesso externo
            self.end_headers()
            
            parametros = urllib.parse.parse_qs(url_analisada.query)
            if 'comando' in parametros:
                comando_completo = parametros['comando'][0]
                ip_cliente = self.client_address[0]
                print(f"[{ip_cliente}] Comando recebido: {comando_completo}")
                
                # Registra no log global ANTES de enviar para o Arduino
                registrar_log(comando_completo, ip_cliente)
                
                # Repassa o comando (Ex: S255\n ou X\n) direto para a placa
                arduino.write((comando_completo + '\n').encode('utf-8'))
                self.wfile.write(b"OK")
            else:
                self.wfile.write(b"Nenhum comando recebido")

def rodar():
    # Deixamos o host vazio '' para aceitar conexões de qualquer PC/Celular da sua rede
    endereco_servidor = ('', 8080)
    httpd = HTTPServer(endereco_servidor, ServidorWeb)
    print("Servidor rodando e aguardando conexoes locais... (Aperte Ctrl+C para parar)")
    
    # Registra inicialização no log
    log_global.insert(0, {
        'mensagem': 'Servidor iniciado',
        'icone': '🚀',
        'hora': datetime.now().strftime('%H:%M:%S'),
        'dispositivo': 'servidor'
    })
    
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        pass
    httpd.server_close()
    print("Servidor parado.")

if __name__ == '__main__':
    rodar()
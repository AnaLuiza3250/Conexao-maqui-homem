import codecs
from http.server import BaseHTTPRequestHandler, HTTPServer
import urllib.parse
import serial
import json

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

class ServidorWeb(BaseHTTPRequestHandler):
    def do_GET(self):
        url_analisada = urllib.parse.urlparse(self.path)
        
        # --- ROTA DE ATUALIZAÇÃO AUTOMÁTICA (MÃO DUPLA) ---
        if url_analisada.path == '/status':
            self.send_response(200)
            self.send_header("Content-type", "application/json")
            self.send_header("Access-Control-Allow-Origin", "*")
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
            
        # --- ROTA DOS COMANDOS DA WEB ---
        else:
            self.send_response(200)
            self.send_header("Content-type", "text/plain")
            self.send_header("Access-Control-Allow-Origin", "*")
            self.end_headers()
            
            parametros = urllib.parse.parse_qs(url_analisada.query)
            if 'comando' in parametros:
                comando_completo = parametros['comando'][0]
                print(f"Recebido da Web: {comando_completo}")
                
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
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        pass
    httpd.server_close()
    print("Servidor parado.")

if __name__ == '__main__':
    rodar()
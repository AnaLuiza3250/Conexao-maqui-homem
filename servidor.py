import codecs
from http.server import BaseHTTPRequestHandler, HTTPServer
import urllib.parse
import serial

# --- CONFIGURAÇÃO DA PORTA SERIAL ---
# Substitua pela sua porta COM correta (ex: 'COM10')
porta_com = 'COM10' 

try:
    arduino = serial.Serial(porta_com, 9600, timeout=1)
    print(f"Conectado com sucesso na porta {porta_com}!")
except Exception as e:
    print(f"ERRO: Nao foi possivel conectar na porta {porta_com}. Verifique se a IDE do Arduino esta com o Monitor Serial aberto.")
    print(e)
    exit()

class ServidorWeb(BaseHTTPRequestHandler):
    def do_GET(self):
        # Configura os cabeçalhos para evitar erros de CORS no navegador
        self.send_response(200)
        self.send_header("Content-type", "text/plain")
        self.send_header("Access-Control-Allow-Origin", "*")
        self.end_headers()
        
        # Analisa a URL para extrair o comando enviado pelo HTML
        url_analisada = urllib.parse.urlparse(self.path)
        parametros = urllib.parse.parse_qs(url_analisada.query)
        
        if 'comando' in parametros:
            # Pega o comando completo (Ex: "S255" ou "C")
            comando_completo = parametros['comando'][0]
            print(f"Recebido da Web: {comando_completo}")
            
            # Envia a string completa para o Arduino via Serial em formato de bytes
            arduino.write((comando_completo + '\n').encode('utf-8'))
            
            self.wfile.write(b"OK")
        else:
            self.wfile.write(b"Nenhum comando recebido")

# Inicia o servidor na porta 8080
def rodar():
    endereco_servidor = ('', 8080)
    httpd = HTTPServer(endereco_servidor, ServidorWeb)
    print("Servidor rodando e aguardando o HTML... (Aperte Ctrl+C para parar)")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        pass
    httpd.server_close()
    print("Servidor parado.")

if __name__ == '__main__':
    rodar()
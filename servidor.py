import serial
import serial.tools.list_ports
from http.server import BaseHTTPRequestHandler, HTTPServer
import urllib.parse

# --- PROCURAR PORTA AUTOMATICAMENTE ---
print("Portas USB detectadas no seu computador:")
portas = list(serial.tools.list_ports.comports())
for p in portas:
    print(f"- {p.device} ({p.description})")

# 1. DIGITE AQUI A SUA PORTA CORRETA (Olhe a lista que vai aparecer no terminal!)
porta_arduino = 'COM10' 

try:
    arduino = serial.Serial(porta_arduino, 9600, timeout=1)
    print(f"\n>>> CONECTADO COM SUCESSO NO ARDUINO NA PORTA {porta_arduino}! <<<\n")
except Exception as e:
    print(f"\n>>> ERRO CRITICO: Nao consegui abrir a porta {porta_arduino}. Verifique se a IDE do Arduino esta com o Monitor Serial aberto ou se a porta mudou. Erro: {e}\n")

class ServidorAutomacao(BaseHTTPRequestHandler):
    def _definir_headers(self):
        self.send_response(200)
        self.send_header('Content-type', 'text/plain')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.end_headers()

    def do_GET(self):
        self._definir_headers()
        url_analisada = urllib.parse.urlparse(self.path)
        parametros = urllib.parse.parse_qs(url_analisada.query)
        
        if 'comando' in parametros:
            cmd = parametros['comando'][0]
            if cmd in ['S', 's', 'C', 'c', 'Q', 'q', 'B', 'b']:
                print(f"Tentando enviar a letra '{cmd}' para o Arduino...")
                try:
                    arduino.write(cmd.encode())
                    print(f"Letra '{cmd}' enviada com sucesso pela USB!")
                except Exception as erro_usb:
                    print(f"Erro ao empurrar dados na USB: {erro_usb}")
                
                self.wfile.write(f"Comando {cmd} enviado!".encode())
                return
                
        self.wfile.write(b"Servidor ativo. Aguardando comandos validos.")

def rodar():
    endereco_servidor = ('', 8080)
    httpd = HTTPServer(endereco_servidor, ServidorAutomacao)
    print("Servidor de automacao pronto e aguardando cliques no HTML...")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        if 'arduino' in globals(): arduino.close()
        print("\nServidor finalizado.")

if __name__ == '__main__':
    rodar()
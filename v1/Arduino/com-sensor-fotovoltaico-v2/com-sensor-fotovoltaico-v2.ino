// --- DECLARAÇÃO DOS PINOS (ATUALIZADOS) ---
const int pinSala = 10;       // PWM (Web Slider)
const int pinJardim = 11;     // PWM (Sensor LDR Automático)
const int pinCozinha = 4;     // Digital (Web ON/OFF)
const int pinDormitorio = 9;  // PWM (Web Slider)
const int pinBanheiro = 7;    // Digital (Web ON/OFF)
const int pinLDR = A5;        // Sensor do Jardim no A5

// --- VARIÁVEIS GLOBAIS PARA SINCRO COM A WEB ---
int valorLDR = 0;
int brilhoSala = 0;
int brilhoQuarto = 0;
int statusCozinha = 0;
int statusBanheiro = 0;
int brilhoJardim = 0;

void setup() {
  Serial.begin(9600);
 
  pinMode(pinSala, OUTPUT);
  pinMode(pinJardim, OUTPUT);
  pinMode(pinCozinha, OUTPUT);
  pinMode(pinDormitorio, OUTPUT);
  pinMode(pinBanheiro, OUTPUT);
 
  digitalWrite(pinSala, LOW);
  digitalWrite(pinJardim, LOW);
  digitalWrite(pinCozinha, LOW);
  digitalWrite(pinDormitorio, LOW);
  digitalWrite(pinBanheiro, LOW);
}

void loop() {
  // --- CONTROLE AUTOMÁTICO DO JARDIM (LDR) ---
  valorLDR = analogRead(pinLDR);
  
  if (valorLDR < 400) {
    digitalWrite(pinJardim, HIGH);
    brilhoJardim = 255; // Salva 255 para a página Web saber que está 100% aceso
  } else {
    digitalWrite(pinJardim, LOW);
    brilhoJardim = 0;   // Salva 0 para a página Web saber que está apagado
  }

  // --- CONTROLE VIA INTERFACE WEB ---
  if (Serial.available() > 0) {
    static String comandoCompleto = ""; 
    char c = Serial.read();
    
    if (c == '\n') {
      comandoCompleto.trim();
      if (comandoCompleto.length() > 0) {
        char comodo = comandoCompleto.charAt(0); 
        
        // 1. ENVIO DE STATUS PARA O PYTHON / WEB
        if (comodo == '?') {
          Serial.print("S:"); Serial.print(brilhoSala);
          Serial.print("|Q:"); Serial.print(brilhoQuarto);
          Serial.print("|C:"); Serial.print(statusCozinha);
          Serial.print("|B:"); Serial.print(statusBanheiro);
          Serial.print("|J:"); Serial.println(brilhoJardim); 
        }
        
        // 2. COMANDO MESTRE: DESLIGAR TUDO
        else if (comodo == 'X') {
          brilhoSala = 0;
          brilhoQuarto = 0;
          statusCozinha = 0;
          statusBanheiro = 0;
          analogWrite(pinSala, 0);
          analogWrite(pinDormitorio, 0);
          digitalWrite(pinCozinha, LOW);
          digitalWrite(pinBanheiro, LOW);
        }
        
        // 3. CÔMODOS DIMMER (SALA E QUARTO)
        else if (comodo == 'S' || comodo == 'Q') {
          String valorTexto = comandoCompleto.substring(1);
          int brilhoWeb = valorTexto.toInt();
          
          if (comodo == 'S') {
            brilhoSala = brilhoWeb;
            analogWrite(pinSala, brilhoSala);
          } else if (comodo == 'Q') {
            brilhoQuarto = brilhoWeb;
            analogWrite(pinDormitorio, brilhoQuarto);
          }
        } 
        
        // 4. CÔMODOS ON/OFF (COZINHA E BANHEIRO)
        else {
          switch (comodo) {
            case 'C': statusCozinha = 1; digitalWrite(pinCozinha, HIGH); break;
            case 'c': statusCozinha = 0; digitalWrite(pinCozinha, LOW); break;
            case 'B': statusBanheiro = 1; digitalWrite(pinBanheiro, HIGH); break;
            case 'b': statusBanheiro = 0; digitalWrite(pinBanheiro, LOW); break;
          }
        }
      }
      comandoCompleto = ""; 
    } 
    else {
      comandoCompleto += c;
    }
  }
}
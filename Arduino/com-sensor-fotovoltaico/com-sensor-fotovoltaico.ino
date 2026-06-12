const int pinSala = 10;       // PWM (Web Slider)
const int pinJardim = 11;     // PWM (Sensor LDR Automático)
const int pinCozinha = 4;    // Digital (Web ON/OFF)
const int pinDormitorio = 9; // PWM (Web Slider)
const int pinBanheiro = 7;  // Digital (Web ON/OFF)

const int pinLDR = A5;       // Sensor do Jardim no A2
int valorLDR = 0;

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
  int valor = analogRead(pinLDR);
  if(valor < 400){
    digitalWrite(pinJardim, HIGH);
    Serial.println(valor);
  } else{
    digitalWrite(pinJardim, LOW);
    Serial.println(valor);
  }



 // --- CONTROLE VIA INTERFACE WEB ---
  if (Serial.available() > 0) {
    static String comandoCompleto = ""; // Guarda o texto que está chegando
    
    char c = Serial.read();
    
    // Se encontrar o fim da linha (\n), processa o comando inteiro
    if (c == '\n') {
      comandoCompleto.trim();
      if (comandoCompleto.length() > 0) {
        char comodo = comandoCompleto.charAt(0); // Pega 'S', 'Q', 'C', 'B', 'c', 'b'
        
        // Se for Sala ou Quarto, extrai o número
        if (comodo == 'S' || comodo == 'Q') {
          String valorTexto = comandoCompleto.substring(1);
          int brilhoWeb = valorTexto.toInt();
          
          if (comodo == 'S') {
            analogWrite(pinSala, brilhoWeb);
          } else if (comodo == 'Q') {
            analogWrite(pinDormitorio, brilhoWeb);
          }
        } 
        // Se forem as outras portas simples (Liga/Desliga)
        else {
          switch (comodo) {
            case 'C': digitalWrite(pinCozinha, HIGH); break;
            case 'c': digitalWrite(pinCozinha, LOW); break;
            case 'B': digitalWrite(pinBanheiro, HIGH); break;
            case 'b': digitalWrite(pinBanheiro, LOW); break;
          }
        }
      }
      comandoCompleto = ""; // Limpa para o próximo comando
    } 
    else {
      // Se não for o fim da linha, vai juntando os caracteres
      comandoCompleto += c;
    }
  }
}
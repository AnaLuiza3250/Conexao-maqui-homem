// Mapeamento dos pinos digitais para os LEDs
const int pinSala = 5;
const int pinCozinha = 4;
const int pinDormitorio = 3;
const int pinBanheiro = 7;
const int pinJardim = 6;



void setup() {
  // Configura a comunicação série (Baud Rate idêntico ao do HTML)
  Serial.begin(9600);
 
  // Define os pinos como saídas digitais
  pinMode(pinSala, OUTPUT);
  pinMode(pinCozinha, OUTPUT);
  pinMode(pinDormitorio, OUTPUT);
  pinMode(pinBanheiro, OUTPUT);
 
  // Inicia com todos os LEDs desligados
  digitalWrite(pinSala, LOW);
  digitalWrite(pinCozinha, LOW);
  digitalWrite(pinDormitorio, LOW);
  digitalWrite(pinBanheiro, LOW);
}

void loop() {
  // Verifica se existem dados disponíveis na porta série
  if (Serial.available() > 0) {
    char comando = Serial.read(); // Lê o comando enviado pelo browser
   
    switch (comando) {
      // --- CONTROLO DA SALA ---
      case 'S':
        digitalWrite(pinSala, HIGH);
        break;
      case 's':
        digitalWrite(pinSala, LOW);
        break;
       
      // --- CONTROLO DA COZINHA ---
      case 'C':
        digitalWrite(pinCozinha, HIGH);
        break;
      case 'c':
        digitalWrite(pinCozinha, LOW);
        break;
       
      // --- CONTROLO DO DORMITORIO ---
      case 'Q':
        digitalWrite(pinDormitorio, HIGH);
        break;
      case 'q':
        digitalWrite(pinDormitorio, LOW);
        break;
       
      // --- CONTROLO DO BANHEIRO ---
      case 'B':
        digitalWrite(pinBanheiro, HIGH);
        break;
      case 'b':
        digitalWrite(pinBanheiro, LOW);
        break;
    }
  }
}
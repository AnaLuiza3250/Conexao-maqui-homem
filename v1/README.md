# Planta Interativa Automatizada (Hardware-Homem)

![Status do Projeto](https://img.shields.io/badge/Status-Conclu%C3%ADdo-brightgreen)
![Tecnologias](https://img.shields.io/badge/Techs-HTML%20%7C%20CSS%20%7C%20Python%20%7C%20Arduino-blue)

Uma interface residencial interativa de automação que conecta o ecossistema Web ao mundo físico de forma **bidirecional**. O projeto permite controlar a iluminação de uma maquete arquitetônica em tempo real, combinando acionamentos digitais (Ligar/Desligar), dimerização via Sliders (PWM) e um sistema inteligente de iluminação automática para o jardim baseado na luz ambiente (LDR).

---

## Como o Projeto Funciona?

O ecossistema opera em um ciclo contínuo de três camadas com comunicação de duas vias:

1. **Front-End (A Planta na Tela):** Uma interface web futurista roxa construída em HTML5 e CSS Grid que replica a planta da casa. Ela envia comandos de controle e, periodicamente, interroga o servidor para atualizar o estado dos botões na tela caso ocorram mudanças físicas.
2. **Back-End (O Servidor Mensageiro):** Um servidor HTTP criado em Python (Porta `8080`) que faz a ponte entre a Web e o Hardware. Ele recebe as requisições `fetch` do navegador, transmite os comandos para a porta USB (Serial) e devolve as respostas de status vindas do Arduino.
3. **Hardware (O Arduino):** O cérebro físico. Além de processar os comandos de liga/desliga e intensidade dos LEDs (PWM), ele monitora de forma autônoma o sensor de luz do jardim e responde instantaneamente quando a Web pergunta o status atual da casa.

---

## Tecnologias e Componentes

### Software / Linguagens
* **HTML5 & CSS3:** Estruturação da planta, efeitos de iluminação (neon/box-shadow) e sliders responsivos.
* **JavaScript (Vanilla):** Lógica assíncrona (`fetch API`) para envio de comandos e polling contínuo de status.
* **Python 3:** Servidor HTTP nativo e biblioteca `pyserial` para gerenciamento da comunicação Serial.

### Hardware
* 1x Placa Arduino (Uno/Nano/Mega)
* 5x LEDs (Sala, Cozinha, Banheiro, Dormitório, Jardim)
* 1x Sensor Fotoresistor LDR (Monitoramento do Jardim)
* 5x Resistores de 220Ω ou 330Ω (para os LEDs)
* 1x Resistor de 10kΩ (para o divisor de tensão do LDR)
* 1x Protoboard e Jumpers de conexão

---

## Mapeamento de Comandos e Pinos

### 1. Controle dos Cômodos
| Cômodo | Pino no Arduino | Tipo de Sinal | Comando Web / Ação |
| :--- | :---: | :---: | :--- |
| **Sala de Estar** | PINO 10 | `PWM` | `S` + Valor (Ex: `S125` para brilho médio) |
| **Dormitório** | PINO 9 | `PWM` | `Q` + Valor (Ex: `Q255` para brilho máximo) |
| **Cozinha** | PINO 4 | `Digital` | `C` (Ligar) / `c` (Desligar) |
| **Banheiro** | PINO 7 | `Digital` | `B` (Ligar) / `b` (Desligar) |
| **Jardim** | PINO 11 | `PWM` | **Automático:** Ativa via LDR quando Leitura < 400 |

### 2. Comandos Especiais do Sistema
* **`?` (Sincronização de Status):** Comando enviado pela Web. O Arduino responde com a string de estados estruturada: `S:[brilho]|Q:[brilho]|C:[0/1]|B:[0/1]|J:[0/255]`
* **`X` (Botão Mestre):** Comando de emergência para apagar imediatamente todas as luzes da casa de uma só vez.

---

## Como Executar o Projeto

### 1. Preparar o Hardware (Arduino)
* Conecte o sensor LDR no pino analógico **A5**.
* Conecte os LEDs nos respectivos pinos informados na tabela de mapeamento.
* Abra o código C++ na IDE do Arduino e faça o upload para a placa.
* **Importante:** Feche o Monitor Serial da IDE após o upload para liberar a porta para o Python.

### 2. Iniciar o Servidor Back-End (Python)
* Abra o terminal na pasta onde está o arquivo `servidor.py`.
* Instale a biblioteca de comunicação serial caso não possua:
  ```bash
  pip install pyserial

* Execute o servidor:
```bash
python servidor.py

```


*(O terminal indicará que a API está rodando com sucesso na porta 8080).*

### 3. Configurar e Abrir a Interface Web (HTML)

* Abra o arquivo HTML do projeto e certifique-se de que a constante `IP_REDE` está apontando para o IP correto da máquina servidora:
```javascript
const IP_REDE = "localhost"; // Se estiver testando no mesmo computador
// const IP_REDE = "10.139.26.146"; // Se for acessar via celular/outro dispositivo no mesmo Wi-Fi

```


* Dê um duplo clique no arquivo `index.html` para abri-lo diretamente no seu navegador.

> ⚠️ **Dica de Rede:** Se for testar pelo celular ou outra máquina da rede e a conexão der *Timeout*, certifique-se de desativar temporariamente o Firewall do Windows ou criar uma regra de entrada permitindo tráfego na porta **8080**.

---

## Licença

Este projeto foi desenvolvido para fins estritamente educacionais, servindo como modelo prático de Engenharia de Usabilidade, Interfaces Homem-Máquina (IHM) e Internet das Coisas (IoT).



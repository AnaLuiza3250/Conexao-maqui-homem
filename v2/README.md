# Planta Interativa Automatizada (Hardware-Homem)

![Status do Projeto](https://img.shields.io/badge/Status-Conclu%C3%ADdo-brightgreen)
![Tecnologias](https://img.shields.io/badge/Techs-HTML%20%7C%20CSS%20%7C%20Python%20%7C%20Arduino-blue)

Uma interface residencial interativa de automação que conecta o ecossistema Web ao mundo físico. O projeto permite controlar lâmpadas (LEDs) de uma maquete arquitetônica em tempo real, seja pelo computador local ou remotamente por qualquer dispositivo (como celulares) conectado à mesma rede Wi-Fi.

---

## Como o Projeto Funciona?

O projeto é dividido em três camadas que se comunicam de forma contínua:

1. **Front-End (A Planta na Tela):** Uma interface web futurista roxa construída em HTML5 e CSS Grid que replica a planta de uma casa. Ao clicar em um cômodo, o JavaScript dispara uma requisição HTTP.
2. **Back-End (O Servidor Mensageiro):** Um servidor HTTP criado em Python que fica escutando as requisições da interface web. Ele quebra a barreira de segurança do navegador e repassa o comando recebido para a porta USB.
3. **Hardware (O Arduino):** Um circuito físico que recebe os caracteres via Comunicação Serial (USB) e aciona individualmente os pinos digitais conectados aos LEDs correspondentes.

---

## Tecnologias e Componentes

### Software / Linguagens
* **HTML5 & CSS3:** Estruturação da planta, efeitos de iluminação (neon/box-shadow) e design responsivo.
* **JavaScript (Vanilla):** Lógica de alternância de estados e envio de requisições assíncronas (`fetch API`).
* **Python 3:** Servidor HTTP nativo e biblioteca `pyserial` para comunicação com o hardware.

### Hardware
* 1x Placa Arduino (Uno/Nano/Mega)
* 4x LEDs (Sala, Cozinha, Banheiro, Dormitório)
* 4x Resistores (220Ω ou 330Ω)
* 1x Protoboard e Jumpers para conexão

---

## Mapeamento de Comandos e Pinos

| Cômodo | Pino no Arduino | Letra (Ligar) | Letra (Desligar) |
| :--- | :---: | :---: | :---: |
| **Sala de Estar** | PINO 2 | `S` | `s` |
| **Cozinha** | PINO 7 | `C` | `c` |
| **Dormitório** | PINO 9 | `Q` | `q` |
| **Banheiro** | PINO 13 | `B` | `b` |

---

## Como Executar o Projeto na sua Máquina

### 1. Preparar o Arduino
* Abra o arquivo do código C++ na IDE do Arduino.
* Faça o upload do código para a sua placa.
* **Atenção:** Feche o Monitor Serial da IDE para não bloquear a porta USB.

### 2. Rodar o Servidor Python
* Abra o terminal na pasta do projeto.
* Certifique-se de que a biblioteca `pyserial` está instalada (`pip install pyserial`).
* Inicie o servidor:
  ```bash
  python servidor.py

---

### 3. Abrir a Interface Web

* Coloque a pasta do projeto dentro do diretório do seu servidor local (ex: `htdocs` do XAMPP) para habilitar o acesso na rede local.
* Inicie o Apache no painel do XAMPP.
* Acesse pelo navegador: `http://localhost/Nome-Da-Sua-Pasta/index.html`

> **Acesso Remoto por Celular:** Substitua `localhost` no código do Front-End pelo endereço de **IP IPv4** da sua máquina (descubra digitando `ipconfig` no terminal) e acesse o mesmo link usando o navegador do seu smartphone conectado ao mesmo Wi-Fi.

---

## Licença

Este projeto foi desenvolvido para fins educacionais e de estudo sobre a relação e comunicação entre interfaces Homem-Máquina (IHM) e Internet das Coisas (IoT).


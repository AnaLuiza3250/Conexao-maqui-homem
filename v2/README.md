# 🏠 Smart Home - Painel de Controle Residencial

Este é um sistema de automação residencial desenvolvido para controlar e monitorar lâmpadas e dimmers (como Sala, Quarto, Cozinha e Banheiro) em tempo real através de uma interface web executada na rede local.

O projeto utiliza uma arquitetura dividida em três partes:
1. **Interface Web (Frontend):** HTML, CSS e JavaScript rodando no servidor Apache (XAMPP).
2. **Servidor de Comunicação (Backend):** Um servidor HTTP em Python que gerencia os comandos e centraliza os logs.
3. **Hardware:** Placa Arduino conectada via porta Serial (USB) controlando os relés e saídas PWM.

---

## 🛠️ Tecnologias Utilizadas

* **Frontend:** HTML5, CSS3 (Design Moderno/Dark Mode), JavaScript (Fetch API para comunicação assíncrona).
* **Servidor Web Local:** XAMPP (Apache para o painel e MySQL/MariaDB para gerenciamento de usuários).
* **Backend de Integração:** Python 3 (Bibliotecas nativas `http.server`, `urllib`, `json` e biblioteca externa `pyserial`).
* **Hardware:** Arduino (C++ nativo) integrado via comunicação Serial.

---

## 📐 Arquitetura do Sistema

O fluxo de comunicação do ecossistema funciona da seguinte forma:


```

[ Navegador Web ] <--- (HTTP / Porta 80) ---> [ Servidor Apache (XAMPP) ]
|
(HTTP / Porta 8080) -> requisições CORS
v
[ Servidor Python ] <--- (Porta Serial / COM) ---> [ Arduino ]

```

---

## 🚀 Como Configurar e Rodar o Projeto

### 1. Pré-requisitos
* **XAMPP** instalado (com Apache e MySQL ativos).
* **Python 3.x** instalado na máquina.
* **IDE do Arduino** instalada para carregar o código na placa.

### 2. Configuração do Banco de Dados (MySQL)
1. Abra o painel do XAMPP e inicialize os módulos **Apache** e **MySQL**.
2. Acesse o `http://localhost/phpmyadmin` no seu navegador.
3. Crie um banco de dados (ex: `smarthome`).
4. Selecione o banco de dados criado, vá até a aba **SQL**, cole o comando abaixo e clique em **Executar**:
   ```sql
   CREATE TABLE usuarios (
       id INT AUTO_INCREMENT PRIMARY KEY,
       usuario VARCHAR(50) NOT NULL UNIQUE,
       senha VARCHAR(255) NOT NULL
   );



### 3. Instalação das Dependências do Python

Abra o Prompt de Comando (CMD) ou terminal e instale a biblioteca necessária para a comunicação serial:

```bash
pip install pyserial

```

### 4. Ajustes nos Arquivos de Configuração

* **No Python (`servidor.py`):** Verifique a linha 9 e certifique-se de que a variável `porta_com` está com a porta correta onde o seu Arduino foi mapeado pelo Windows (Ex: `'COM10'`, `'COM3'`).
* **No JavaScript (`app.js`):** Na linha 2, certifique-se de que a variável `IP_REDE` está definida como `"localhost"` se estiver rodando no mesmo computador, ou com o IP local do PC (`192.168.X.X`) caso queira controlar o painel usando um smartphone conectado ao mesmo Wi-Fi.

---

## 🚦 Executando o Sistema

Siga rigorosamente a ordem abaixo para evitar conflitos na porta serial do hardware:

1. **Conecte o Arduino** na porta USB do computador.
2. Certifique-se de que o **Monitor Serial da IDE do Arduino está FECHADO** (caso contrário, o Python não conseguirá se conectar).
3. Abra o terminal na pasta do projeto e inicialize o servidor Python:
```bash
python servidor.py

```


*Você deverá ver a mensagem: `Conectado com sucesso na porta COMXX!`.*
4. Mova a pasta do seu projeto para dentro do diretório do XAMPP (`C:\xampp\htdocs\`).
5. Acesse o painel pelo seu navegador através do endereço:
```text
http://localhost/nome-da-sua-pasta/index.php

```

---

## 📡 Estrutura de Endpoints (API Python)

O servidor Python escuta na porta `8080` e responde aos seguintes caminhos:

* **`GET /status`:** Retorna o estado atual de todos os cômodos enviados pelo Arduino em formato JSON.
* *Exemplo de retorno:* `{"S": 120, "Q": 0, "C": 1, "B": 0, "J": 255}`


* **`GET /log`:** Retorna a lista dos últimos 80 eventos registrados na sessão.
* **`GET /log?limpar=1`:** Limpa o histórico de logs armazenados na memória.
* **`GET /?comando=X`:** Envia um comando em string diretamente para o Arduino via Serial e gera um registro no log.

---

## ⚠️ Resolução de Problemas Comuns

* **Erro `PermissionError: [Errno 13] ...` no Python:** O Monitor Serial da IDE do Arduino ou outro programa está usando a porta COM. Feche tudo e reinicie o script Python.
* **Painel travado em "Desconectado":** Verifique se o servidor Python está rodando no terminal ou se o navegador está bloqueando as requisições por falta de correspondência da variável `IP_REDE` no `app.js`.
* **Erro `WriteFile failed (PermissionError)`:** O Arduino sofreu uma queda de energia ou mau contato no cabo USB. Desplugue o cabo, conecte novamente e reinicie o script Python.


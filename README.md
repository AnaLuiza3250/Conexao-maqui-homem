# 🏠 Sistema de Automação Residencial - Smart Home

Este repositório contém o ciclo de desenvolvimento de um ecossistema de automação residencial para monitoramento e controle de cargas (lâmpadas e dimmers) em tempo real, integrando interface web, servidor local e microcontrolador Arduino.

O projeto foi construído em duas etapas evolutivas principais (v1 e v2) para fins de aprendizado e escalabilidade.

---

## 📂 Estrutura do Repositório

```text
📁 smart-home-root/
│
├── 📁 v1/                  # Primeira versão estável (Foco na comunicação direta)
│   ├── 📁 Arduino/         # Código C++ (.ino) carregado na placa
│   ├── 📄 index.html       # Interface estática simples do painel
│   ├── 📄 servidor.py      # Servidor HTTP bruto em Python (comunicação serial)
│   └── 📄 README.md        # Documentação específica da v1
│
└── 📁 v2/                  # Segunda versão estável (Foco em segurança e logs)
    ├── 📁 Arduino/         # Código C++ do microcontrolador adaptado para a v2
    ├── 📄 app.js           # Lógica do painel dinâmico e requisições assíncronas (CORS)
    ├── 📄 style.css        # Estilização moderna em Dark Mode do painel
    ├── 📄 servidor.py      # Servidor HTTP Python robusto (com suporte a CORS e rota de logs)
    ├── 📁 scripts-php/     # Arquivos de backend do XAMPP (index.php, login.php, conexao.php, etc.)
    └── 📄 README.md        # Documentação específica da v2

```

---

## 📋 Comparativo das Versões

| Recurso / Módulo | Versão 1 (v1) | Versão 2 (v2) |
| --- | --- | --- |
| **Banco de Dados (MySQL)** | ❌ Não possui (Acesso direto) | Possui (Autenticação e controle de usuários) |
| **Interface do Usuário** | HTML estático simples | Dashboard Dinâmico em PHP/CSS moderno |
| **Segurança** | Qualquer dispositivo na rede acessa direto | Tela de Login/Cadastro com hash de senhas |
| **Histórico de Eventos** | Apenas print no console do Python | Terminal de logs integrado diretamente no painel web |
| **Tratamento de CORS** | Limitado (Pode causar bloqueio no navegador) | Completo (Suporte a requisições Cross-Origin via OPTIONS) |

---

## 🛠️ Tecnologias Utilizadas

* **Frontend:** HTML5, CSS3, JavaScript (Fetch API para requisições assíncronas).
* **Servidor Web Local:** XAMPP (Módulo Apache e módulo MySQL/MariaDB).
* **Backend de Integração:** Python 3 (Módulos nativos `http.server`, `urllib`, `json` e biblioteca externa `pyserial`).
* **Hardware:** Arduino (Pinos Digitais para Relés e Pinos PWM para Dimmers).

---

## 🚀 Como Executar Cada Versão

> ⚠️ **Importante:** Em ambas as versões, certifique-se de que o **Monitor Serial da IDE do Arduino está fechado** antes de iniciar os scripts Python para que a porta COM não seja bloqueada.

### Rodando a Versão 1 (v1)

A `v1` é ideal para testes rápidos de hardware sem a necessidade de configurar servidores web complexos ou bancos de dados.

1. Carregue o código contido em `/v1/Arduino/` na sua placa.
2. Certifique-se de que a porta correta (Ex: `COM10`) está definida no arquivo `servidor.py`.
3. Execute o servidor de integração:
```bash
cd v1
python servidor.py

```


4. Dê um duplo clique no arquivo `index.html` para abrir o painel diretamente no navegador.

### Rodando a Versão 2 (v2)

A `v2` é o sistema completo e simula um ambiente de produção real com controle de acessos.

1. Carregue o código contido em `/v2/Arduino/` na sua placa.
2. Mova todo o conteúdo da pasta `v2` para o diretório raiz do seu servidor local Apache (Ex: `C:\xampp\htdocs\smarthome\`).
3. Abra o **phpMyAdmin** (`http://localhost/phpmyadmin`), crie um banco de dados e execute o seguinte script na aba SQL para criar a tabela de credenciais:
```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

```


4. No arquivo `app.js`, configure a variável `IP_REDE` para `"localhost"`.
5. Execute o servidor de integração Python da v2 em seu terminal:
```bash
cd v2
python servidor.py

```


6. Acesse o sistema pelo navegador através da URL local do Apache:
```text
http://localhost/smarthome/index.php

```



---

## 🚦 Resolução de Erros Críticos de Comunicação

* **Erro `WriteFile failed (PermissionError)` no Python:** Ocorre se o cabo USB do Arduino sofrer um mau contato ou oscilação de energia enquanto o servidor está escrevendo dados. **Solução:** Desplugue o Arduino da entrada USB, conecte-o novamente e reinicie o script `servidor.py`.
* **Erro `#1046 - Nenhum banco de dados foi selecionado` no MySQL:** Ocorre ao tentar rodar a Query de criação da tabela sem definir o alvo. **Solução:** No menu esquerdo do phpMyAdmin, clique primeiro no nome do seu banco de dados para depois abrir a aba SQL e rodar o script.
* **Painel travado em "Desconectado":** Indica que o arquivo JavaScript não está localizando o servidor Python. Verifique se o script Python está ativo na porta `8080` e se a variável `IP_REDE` no `app.js` confere com o host atual.

---

### Dica de ouro:

Se você já tiver arquivos `README.md` dentro das subpastas `/v1/` e `/v2/`, mantenha-os lá! Eles complementam esse arquivo principal, funcionando como guias detalhados de cada pedaço do projeto. Se colocar esse código acima na raiz de tudo, seu portfólio ou entrega de trabalho vai ficar com nível super profissional.
# ERP Estoque

<p align="center">
<img src="https://skillicons.dev/icons?i=php,laravel,vue,postgres,redis,docker,git&perline=7" />
</p>

Projeto **fullstack** para gerenciamento de **produtos, compras e vendas**, composto por **Backend (API REST)** e **Frontend**, orquestrados com **Docker**.

O sistema permite registrar movimentações de estoque e acompanhar automaticamente o impacto dessas operações no **custo médio dos produtos, estoque disponível e lucro obtido nas vendas**.

⚠️ Este projeto foi desenvolvido e testado em ambiente **WSL (Windows Subsystem for Linux)** utilizando **Ubuntu no Windows**.

---

## 🌐 Acesso ao Projeto (Homologação)

O projeto também está disponível online para testes.

**Frontend**  
https://erp-estoque-chi.vercel.app/

**API**  
https://erp-estoque.onrender.com

**Documentação da API**  
https://erp-estoque.onrender.com/docs/api

⚠️ O primeiro acesso à API pode levar alguns segundos, pois o **Render hiberna serviços gratuitos após período de inatividade**.

---

## 📦 Estrutura do Projeto

```
.
├── Backend
│   ├── app
│   ├── routes
│   ├── Dockerfile
│   ├── .env.example
│   └── ...
│
├── Frontend
│   ├── src
│   ├── Dockerfile
│   ├── .env.example
│   └── ...
│
├── docker-compose.yml
├── import_file_endpoints_insomnia.json
└── README.md
```

---

## 🛠 Tecnologias

### Backend
- Laravel 12
- PHP 8.4
- PostgreSQL
- Redis

### Frontend
- Vue 3
- PrimeVue

### Ferramentas
- Docker
- Docker Compose
- Insomnia / Postman

---

## 🚀 Executando localmente

### 1. Clonar o repositório

```
git clone https://github.com/YoungC0DE/ERP_Estoque.git
cd ERP_Estoque
```

---

### 2. Criar arquivos de ambiente

Tanto o **Frontend** quanto o **Backend** utilizam arquivos `.env`.

Crie os arquivos a partir dos exemplos fornecidos:

```
Backend/.env.example  → Backend/.env
Frontend/.env.example → Frontend/.env
```

---

### 3. Subir os containers

Na **raiz do projeto**, execute:

```
docker compose up -d
```

Esse comando iniciará todos os serviços necessários:

- API Laravel
- PostgreSQL
- Redis
- Frontend

---

### 4. Instalar dependências do Backend

Acesse o container da API:

```
docker compose exec api bash
```

Dentro do container execute:

```
composer install -o
```

---

## 📚 Documentação da API

A documentação da API é gerada automaticamente utilizando **Scramble**.

Ambiente local:

```
http://localhost:8000/docs
```

Nesta página é possível visualizar:

- Lista de endpoints
- Parâmetros de requisição
- Exemplos de payload
- Exemplos de resposta da API

A documentação é gerada diretamente a partir dos **Controllers e Requests**, garantindo que esteja sempre sincronizada com o código da aplicação.

---

## 🔎 Testando a API

Para testar os endpoints manualmente:

1. Abra o **Insomnia** ou **Postman**
2. Importe o arquivo:

```
import_file_endpoints_insomnia.json
```

O arquivo está localizado **na raiz do projeto** e contém uma coleção com todos os endpoints disponíveis na API.

### Variáveis de ambiente no Insomnia

Caso utilize o **Insomnia em Scratch Pad (modo local/offline)**, pode ser necessário configurar manualmente as variáveis de ambiente.

Abra:

```
Manage Environments
```

E adicione:

```
{
  "url_base_local": "http://localhost:8000/api/v1",
  "url_base_hml": "https://erp_estoque.onrender.com/api/v1"
}
```

---

## 🧪 Testando o Frontend

Ambiente local:

```
http://localhost:5173
```

A interface permite:

- Cadastro de produtos
- Visualização do estoque
- Registro de compras
- Registro de vendas

---

## ⚙️ Funcionalidades da API

A API fornece endpoints para:

- Cadastro de produtos
- Registro de compras
- Registro de vendas
- Controle de estoque
- Cálculo automático de **custo médio**
- Cálculo de **lucro nas vendas**

A API segue o padrão **REST**, com **versionamento de rotas (`/api/v1`)**, utilizando:

- Controllers
- Requests para validação
- Models

---

## 🎨 Interface do Frontend

O layout do frontend foi **inspirado no template Sakai**, disponibilizado pelo **PrimeVue**, adaptado para atender às necessidades deste projeto.

A proposta foi manter uma interface **simples, limpa e focada em produtividade**, padrão comum em sistemas administrativos e ERPs.

---

## 🤖 Uso de Inteligência Artificial

Durante o desenvolvimento foram utilizadas ferramentas baseadas em **Inteligência Artificial** como suporte de produtividade:

- GitHub Copilot
- ChatGPT
- Cursor

Essas ferramentas foram utilizadas para **revisão de código, auxílio técnico e produtividade**, sem geração automática integral do projeto.

---

## 📄 Licença

Projeto desenvolvido para **fins educacionais e avaliação técnica**.

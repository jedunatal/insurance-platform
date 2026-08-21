# 🛠️ Guia de Instalação e Execução — Insurance Platform

Este guia descreve o processo de configuração do ambiente local utilizando o **Laravel Sail** (Docker no Ubuntu/WSL).

---

## 1. Pré-requisitos

* Docker Engine & Docker Compose instalados e em execução.
* Git.
* WSL2 (caso esteja utilizando Windows).

---

## 2. Passo a Passo de Instalação

### Passo 1: Clonar o Repositório
```bash
git clone <url-do-repositorio>
cd insurance-platform
```

### Passo 2: Configurar o Arquivo de Ambiente
Copie o arquivo `.env.example` para `.env`:
```bash
cp .env.example .env
```

### Passo 3: Inicializar os Containers com Laravel Sail
```bash
./vendor/bin/sail up -d
```

### Passo 4: Instalar Dependências do PHP (se necessário)
```bash
./vendor/bin/sail composer install
```

### Passo 5: Gerar a Chave da Aplicação
```bash
./vendor/bin/sail artisan key:generate
```

### Passo 6: Executar as Migrations e Seeders
```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

### Passo 7: Criar o Link Simbólico de Armazenamento (Storage)
```bash
./vendor/bin/sail artisan storage:link
```

### Passo 8: Compilar os Assets Frontend (Tailwind / Vite)
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

---

## 3. Acessos aos Serviços

* **Aplicação Web:** [http://localhost](http://localhost)
* **Mailpit (Captura de E-mails):** [http://localhost:8025](http://localhost:8025)
* **MySQL:** Porta `3306` (usuário: `sail`, senha: `password`, base: `insurance_platform`)

---

## 4. Comandos Úteis do Sail

```bash
# Executar migrations
./vendor/bin/sail artisan migrate

# Executar a suíte de testes automatizados
./vendor/bin/sail artisan test

# Entrar no terminal interativo do PHP (Tinker)
./vendor/bin/sail artisan tinker

# Modo de desenvolvimento do Frontend (Hot Reload)
./vendor/bin/sail npm run dev

# Parar os containers
./vendor/bin/sail down
```

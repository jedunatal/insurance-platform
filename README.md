# 🛡️ Insurance Platform

> Plataforma de Consultoria e Corretagem de Seguros desenvolvida com foco em escalabilidade, automação e experiência do usuário.

![Laravel](https://img.shields.io/badge/Laravel-13-red)
![PHP](https://img.shields.io/badge/PHP-8.5-blue)
![Livewire](https://img.shields.io/badge/Livewire-3-purple)
![MySQL](https://img.shields.io/badge/MySQL-8.4-orange)
![Docker](https://img.shields.io/badge/Docker-Ready-blue)
![License](https://img.shields.io/badge/License-MIT-green)

---

# 📖 Sobre o projeto

O Insurance Platform é uma plataforma moderna voltada para consultoria e corretagem de seguros.

O objetivo do projeto é oferecer um ecossistema digital capaz de conectar corretoras, consultores e clientes em uma única plataforma, automatizando processos operacionais e melhorando a experiência do usuário.

O projeto foi concebido para evoluir futuramente para um modelo SaaS (Software as a Service).

---

# 🎯 Objetivos

- Gestão de Leads;
- Gestão de Clientes;
- Gestão de Cotações;
- Gestão de Apólices;
- Gestão de Sinistros;
- Gestão de Renovações;
- Portal do Cliente;
- Gestão Documental;
- CRM;
- Dashboards;
- Notificações;
- Integração com WhatsApp;
- Automações de Processos;
- Arquitetura preparada para Multi-Tenant.

---

# 🏗️ Stack Tecnológica

## Backend

- PHP 8.5+
- Laravel 13

## Frontend

- Blade
- Livewire 3
- Alpine.js
- Tailwind CSS

## Componentes

- Filament 4

> O projeto não utiliza Filament Resources como painel administrativo tradicional.
>
> O Filament é utilizado como biblioteca de componentes para:
>
> - Forms;
> - Tables;
> - Actions;
> - Widgets;
> - Infolists;
> - Modals.

---

# 🐳 Infraestrutura

- Docker
- Laravel Sail
- MySQL
- Redis
- Mailpit
- Queue Workers
- Scheduler

Ambiente de desenvolvimento:

- Ubuntu (WSL)

---

# 🏛️ Arquitetura

O projeto segue princípios de:

- SOLID;
- DRY;
- KISS;
- Clean Architecture;
- Baixo Acoplamento;
- Alta Coesão.

Estrutura principal:

```

app/

├── Actions/
├── DTOs/
├── Enums/
├── Livewire/
├── Models/
├── Policies/
├── Services/
├── Traits/
├── ValueObjects/

```

---

# 🎨 Design System

Paleta principal:

Primary:

```

#295384

```

Secondary:

```

#B99B6C

```

O design do sistema busca uma experiência corporativa, moderna e intuitiva.

---

# 🚀 Roadmap

## Sprint 0

- [x] Infraestrutura
- [x] Docker
- [x] Laravel
- [x] MySQL
- [x] Redis
- [x] Mailpit

## Sprint 1

- [ ] Layout Base
- [ ] Dashboard
- [ ] Sidebar
- [ ] Topbar

## Sprint 2

- [ ] CRM

## Sprint 3

- [ ] Catálogo

## Sprint 4

- [ ] Cotações

## Sprint 5

- [ ] Apólices

## Sprint 6

- [ ] Sinistros

---

# 📋 Status do Projeto

🚧 Em desenvolvimento.

Primeira versão focada na construção da fundação da plataforma e MVP operacional.

---

# 🤝 Filosofia do Projeto

Este projeto não busca apenas criar um sistema de gestão.

O objetivo é construir uma plataforma moderna para o mercado de seguros, priorizando:

- Escalabilidade;
- Manutenibilidade;
- Segurança;
- Performance;
- Boa experiência do usuário;
- Evolução contínua.

---

# 👨‍💻 Desenvolvimento

Clone o projeto:

```bash
git clone <repository>
```

Entre no diretório:

```bash
cd insurance-platform
```

Suba os containers:

```bash
./vendor/bin/sail up -d
```

Execute as migrations:

```bash
./vendor/bin/sail artisan migrate
```

Acesse:

```

http://localhost

```

---

# 📌 Licença

Projeto desenvolvido para fins de estudo, evolução técnica e construção de produto digital.

---

Desenvolvido com ❤️ utilizando Laravel.

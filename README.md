# 🛡️ Insurance Platform

> **Plataforma Completa de Gestão Estratégica e Operacional para Corretoras de Seguros.**  
> Desenvolvida com foco em alta performance, automação de processos, retenção de carteira e experiência fluida para o corretor.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Filament](https://img.shields.io/badge/Filament_Components-F2994A?style=for-the-badge)
![Docker](https://img.shields.io/badge/Docker_Sail-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Tests](https://img.shields.io/badge/Tests-36_Passed-success?style=for-the-badge)

---

## 📚 Documentação Técnica Completa

Para aprofundar na estrutura e decisões de engenharia, consulte os guias dedicados:

* 🏛️ **[Arquitetura do Sistema](docs/ARCHITECTURE.md)**: Padrões de Actions, DTOs, Enums, Filament como UI library e modelo Multi-Tenant.
* 📦 **[Módulos Funcionais](docs/MODULES.md)**: Detalhamento de cada funcionalidade operacional e regras de negócio.
* 🛠️ **[Guia de Instalação](docs/INSTALLATION.md)**: Passo a passo de setup com Docker Sail, MySQL, Redis e Mailpit.
* 🧪 **[Guia de Testes Automatizados](docs/TESTING.md)**: Execução da suíte de testes de integração e cobertura de código.

---

## 🚀 Principais Módulos & Recursos

### 1. 👥 CRM & Clientes em Potencial (`Leads`)
* Funil visual de atendimento (*Novo $\rightarrow$ Em Atendimento $\rightarrow$ Cotado $\rightarrow$ Ganho/Convertido $\rightarrow$ Perdido*).
* Ação de **Conversão em Segurado** com 1 clique.

### 2. 🛡️ Gestão de Segurados (`Insureds`)
* Cadastro completo de Pessoa Física (PF) e Pessoa Jurídica (PJ) com máscaras de validação e endereços de cobrança/risco.
* Ficha 360º com histórico de apólices, sinistros, cotações e documentos.

### 3. ⚡ Cotações Multi-Seguradoras & Comparativo
* Simulação e comparação de opções de cálculo (*Porto Seguro, Allianz, Tokio Marine, Bradesco, etc.*).
* Comparativo lado a lado de **Prêmio Total**, **Franquia**, **Carro Reserva**, **Vidros** e **RCF-V**.
* **Geração de PDF Comercial de Proposta** personalizado com a marca da corretora.
* **Aceite com 1 Clique:** Conversão direta da opção aceita em Apólice emitida.

### 4. 📄 Apólices & Certificados Oficiais em PDF
* Cálculo tributário automático de **IOF por ramo** (*Auto/Residencial/Empresarial: 7.38%, Vida/Saúde: 0.38%, Rural: 0.00%*).
* Cálculo reativo de comissão de corretagem e **Split de Comissão com Produtores/Parceiros**.
* **Campos Especializados:**
  * **Auto:** Placa, Código FIPE, Chassi, Ano Fab/Modelo, CEP Pernoite, Condutor Principal.
  * **Imóvel:** Tipo, CEP de Risco, Construção e Alarme Monitorado.
  * **Vida:** Quadro de Beneficiários com partilha percentual.
* **Emissão de Certificado de Seguro em PDF (DomPDF)** com autenticação criptográfica SHA-256 e termos SUSEP.

### 5. 🔄 Esteira Ativa de Renovações (*Retention Pipeline*)
* **Quadro Kanban:** *A Contatar $\rightarrow$ Em Cotação $\rightarrow$ Proposta Enviada $\rightarrow$ Renovada $\rightarrow$ Perdida*.
* **Sincronização Automática:** Alimenta o pipeline com apólices a vencer nos próximos 45 dias.
* **⚡ Renovação 1-Clique:** Clona a apólice com +1 ano de vigência, nova numeração e grade de parcelas.
* **Registro de Motivo de Perda:** Análise de churn e não-renovação.

### 6. ⚠️ Sinistros & Regulação (`Claims`)
* Abertura de aviso de sinistro vinculada à apólice e segurado.
* Controle de franquia, terceiros envolvidos, estimativa de prejuízo vs indenização paga.

### 7. 💰 Módulo Financeiro & Comissões
* Grade detalhada de parcelas com vencimentos projetados e ajuste de centavos.
* Modal de liquidação com registro de comissões creditadas.
* KPIs de faturamento a vencer, comissões recebidas e parcelas em atraso.

### 8. 📁 Gestão Eletrônica de Documentos (GED)
* Módulo polimórfico de upload e visualização de documentos (*CNH, CRLV, Apólices, Laudos, B.O., Fotos e Orçamentos*).

### 9. 🔍 Busca Global & 🔔 Central de Notificações
* **Busca Global (`Ctrl + K` / `Cmd + K`):** Localização instantânea em 4 módulos com normalização de CPF/CNPJ.
* **Sino de Alertas:** Notificações automáticas de apólices a vencer, cobranças pendentes e novos leads.

---

## 🏗️ Stack Tecnológica

| Camada | Tecnologia |
| :--- | :--- |
| **Backend** | PHP 8.5+ • Laravel 12 • SQLite (Testes) / MySQL 8.4 (Produção) |
| **Frontend** | Laravel Livewire 3 • Alpine.js • Tailwind CSS (Dark Mode nativo) |
| **Componentes** | Filament Forms, Tables, Actions, Schemas, Infolists & Notifications |
| **PDF Engine** | Barryvdh Laravel DomPDF |
| **Ambiente** | Docker & Laravel Sail (Ubuntu / WSL) |

### 🎨 Design System
* **Primary (Navy Blue):** `#295384`
* **Secondary (Gold):** `#B99B6C`

---

## ⚡ Instalação Rápida

```bash
# 1. Clone o repositório
git clone <url-do-repositorio>
cd insurance-platform

# 2. Configure o ambiente
cp .env.example .env

# 3. Inicialize os containers
./vendor/bin/sail up -d

# 4. Execute migrations e seeds
./vendor/bin/sail artisan migrate --seed

# 5. Crie o link do storage
./vendor/bin/sail artisan storage:link

# 6. Compile os assets frontend
./vendor/bin/sail npm run build
```

Acesse a aplicação em **[http://localhost](http://localhost)**.

---

## 🧪 Testes Automatizados

```bash
# Executar toda a suíte de testes (33 testes, 230 asserções)
./vendor/bin/sail artisan test
```

---

## 📄 Licença

Este projeto é desenvolvido sob a licença [MIT](LICENSE).

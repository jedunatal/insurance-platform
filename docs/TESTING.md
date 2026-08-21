# 🧪 Guia de Testes Automatizados — Insurance Platform

A plataforma conta com uma suíte abrangente de testes automatizados de integração e funcionalidade construída com **PHPUnit** e **Livewire Testing Utilities**.

---

## 1. Executando os Testes

Para executar toda a suíte de testes via Laravel Sail:

```bash
./vendor/bin/sail artisan test
```

Para executar testes de um arquivo ou módulo específico:

```bash
# Testes do Módulo de Cotações, Renovações e GED
./vendor/bin/sail artisan test --filter=BrokerEvolutionModulesTest

# Testes de Emissão e Geração de PDF da Apólice
./vendor/bin/sail artisan test --filter=PolicyDocumentTest

# Testes do Módulo Financeiro e Parcelamento
./vendor/bin/sail artisan test --filter=FinancialModuleTest

# Testes da Busca Global Multidomínio
./vendor/bin/sail artisan test --filter=GlobalSearchTest

# Testes de Domínio de Apólices e Sinistros
./vendor/bin/sail artisan test --filter=PolicyAndClaimDomainTest

# Testes de Acessibilidade de Rotas HTTP
./vendor/bin/sail artisan test --filter=InsurancePlatformRoutesTest
```

---

## 2. Cobertura dos Testes

| Arquivo de Teste | Módulos & Funcionalidades Validadas |
| :--- | :--- |
| [`BrokerEvolutionModulesTest.php`](file:///home/jebrito/insurance-platform/tests/Feature/BrokerEvolutionModulesTest.php) | Esteira de Renovações (Kanban + 1-clique), Registro de Perda, Cotações Multi-Seguradoras, Conversão em Apólice, Upload/Delete no GED, Alertas do Corretor. |
| [`RenewalsPipelineTest.php`](file:///home/jebrito/insurance-platform/tests/Feature/RenewalsPipelineTest.php) | Filtros de Renovações Próximas (30 dias), Mutators de conversão BRL em decimais, Máscaras e Validação. |
| [`PolicyDocumentTest.php`](file:///home/jebrito/insurance-platform/tests/Feature/PolicyDocumentTest.php) | Visualização HTML de Apólice, Geração e Streaming de PDF DomPDF, Download de Arquivo. |
| [`FinancialModuleTest.php`](file:///home/jebrito/insurance-platform/tests/Feature/FinancialModuleTest.php) | IOF por Ramo, Geração da Grade de Parcelas com ajuste de centavos, Liquidação e Baixa de Comissões. |
| [`GlobalSearchTest.php`](file:///home/jebrito/insurance-platform/tests/Feature/GlobalSearchTest.php) | Busca multidomínio (Leads, Segurados, Apólices, Sinistros), Reconhecimento de CPF/CNPJ com ou sem máscara, Caracteres especiais. |
| [`PolicyAndClaimDomainTest.php`](file:///home/jebrito/insurance-platform/tests/Feature/PolicyAndClaimDomainTest.php) | Criação de Apólice com coberturas flexíveis, Cadastro de Sinistros com regulação. |
| [`LeadConversionTest.php`](file:///home/jebrito/insurance-platform/tests/Feature/LeadConversionTest.php) | Conversão de Leads em Segurados, Transição de Status do Funil. |
| [`InsurancePlatformRoutesTest.php`](file:///home/jebrito/insurance-platform/tests/Feature/InsurancePlatformRoutesTest.php) | Retorno HTTP 200 em todas as rotas e telas da plataforma. |

---

## 3. Convenções para Novos Testes

* **Banco de Dados:** Todos os testes de Feature utilizam a trait `RefreshDatabase` e rodam em memória (`sqlite :memory:`).
* **Transações:** Ações devem ser executadas através do container (`app(MinhaAction::class)->execute(...)`).
* **Testes Livewire:** Validar propriedades reativas, chamadas de método (`call(...)`), asserções de view (`assertSee(...)`) e renderização sem exceções.

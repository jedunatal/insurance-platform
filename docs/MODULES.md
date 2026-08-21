# 📦 Módulos Funcionais — Insurance Platform

Este documento detalha o funcionamento, regras de negócio e telas de cada módulo operacional do sistema.

---

## 1. CRM & Clientes em Potencial (`Leads`)
* **Objetivo:** Gestão de oportunidades de seguro desde o primeiro contato até a conversão.
* **Ciclo de Vida / Status:**
  * `Novo` $\rightarrow$ `Em Atendimento` $\rightarrow$ `Cotado` $\rightarrow$ `Convertido` $\rightarrow$ `Perdido`.
* **Funcionalidades:**
  * Cadastro de proponente com contato e produto de interesse.
  * Agendamento de retorno com `next_contact_at`.
  * Ação de **Conversão em Segurado** com 1 clique (`ConvertLeadToInsuredAction`).

---

## 2. Segurados (`Insureds`)
* **Objetivo:** Cadastro central de clientes pessoa física (PF) e jurídica (PJ).
* **Dados Fiscais e Cadastrais:**
  * CPF / CNPJ com máscara e validação de documento.
  * Tipo de pessoa (`PF` ou `PJ`), data de nascimento/fundação.
  * Endereço completo com CEP para cobrança e análise de risco.
* **Ficha de Atendimento:** Histórico de apólices, sinistros, cotações, renovações e anexo de documentos (GED).

---

## 3. Cotações & Comparativo Multi-Seguradoras (`Quotes`)
* **Objetivo:** Simulação e comparação lado a lado de cálculos de diferentes seguradoras parceiras para envio de propostas comerciais aos clientes.
* **Funcionalidades:**
  * Cadastro de múltiplas opções de seguradoras (*Porto Seguro, Allianz, Tokio Marine, Bradesco, etc.*).
  * Comparação detalhada: **Prêmio Total**, **Franquia (Normal / Reduzida)**, **Carro Reserva**, **Vidros**, **RCF-V Danos Materiais e Corporais**, **Destaques / Diferenciais**.
  * Destaque automático da opção de **Melhor Custo x Benefício**.
  * **Exportação em PDF da Proposta:** Documento comercial elegante gerado em tempo real com a marca da corretora.
  * **Aprovação & Conversão em Apólice:** 1 clique na opção aprovada pelo cliente para emitir automaticamente o contrato de seguro.

---

## 4. Apólices & Certificados de Seguro (`Policies`)
* **Objetivo:** Gestão do ciclo de vida das apólices de seguro emitidas.
* **Cálculos Fiscais & Tributários:**
  * **Ramo do Seguro:** Alíquota de IOF padrão automática (Automóvel/Residencial/Empresarial: 7.38%, Vida/Saúde: 0.38%, Rural: 0.00%).
  * Cálculo reativo do Prêmio Total (`net_premium + iof_amount`).
  * Cálculo da Comissão da Corretora (`commission_amount = net_premium * commission_percentage`).
  * **Split de Comissão:** Rateio e cálculo de repasse para Produtores / Parceiros comerciais externos.
* **Campos Especializados por Ramo:**
  * **Automóvel:** Placa, Código FIPE, Marca, Modelo, Ano Fab/Modelo, Chassi, CEP de Pernoite e Condutor Principal.
  * **Residencial / Empresarial:** Tipo de Imóvel, CEP de Risco, Construção e Alarme Monitorado.
  * **Vida / Saúde:** Quadro dinâmico de Beneficiários com partilha percentual.
* **Emissão de PDF Oficial:**
  * Certificado de Seguro formal com todas as coberturas discriminadas, grade de vencimentos, declarações SUSEP e autenticação digital com Hash SHA-256.

---

## 5. Esteira Ativa de Renovações (`Renewals`)
* **Objetivo:** Pipeline de retenção de carteira para evitar perda de clientes nos prazos de vencimento.
* **Quadro Kanban Interativo:**
  * `A Contatar (45 dias)` $\rightarrow$ `Em Cotação` $\rightarrow$ `Proposta Enviada` $\rightarrow$ `Renovada` $\rightarrow$ `Não Renovada / Perdida`.
* **Automações:**
  * **Sincronização Automática:** Varre apólices ativas nos próximos 45 dias e alimenta o pipeline.
  * **Renovação em 1 Clique:** Duplica a apólice anterior incrementando vigência em +1 ano, gerando novo número, grade de parcelas e preservando histórico.
  * **Registro de Motivo de Perda:** Documentação estruturada do motivo de perda com notas.

---

## 6. Sinistros & Regulação (`Claims`)
* **Objetivo:** Acompanhamento do aviso, vistoria e indenização de sinistros.
* **Funcionalidades:**
  * Registro de ocorrência vinculado à apólice e segurado.
  * Tipo do evento (Colisão, Roubo/Furto, Incêndio, Terceiros, Danos Elétricos, etc.).
  * Controle de valores: Estimativa inicial de prejuízo vs Valor final indenizado pela seguradora.
  * Controle de franquia paga e registro de terceiros envolvidos.

---

## 7. Módulo Financeiro & Liquidação de Comissões (`Financial`)
* **Objetivo:** Faturamento de apólices e controle de recebimento de comissões de corretagem.
* **Grade de Parcelas:**
  * Divisão exata do prêmio e da comissão prevista em $N$ parcelas mensais, com ajuste de centavos na primeira parcela.
* **Ação de Liquidação / Baixa:**
  * Modal para registrar data de pagamento, valor da comissão efetivamente creditada e notas fiscais.
* **KPIs no Topo:** Total a Receber, Comissões Recebidas, Faturamento a Vencer e Inadimplência.

---

## 8. Gestão Eletrônica de Documentos (`GED / Attachments`)
* **Objetivo:** Guarda e organização de arquivos digitais vinculados a qualquer registro do sistema.
* **Categorias:** CNH, CRLV, Apólice da Seguradora, Laudo de Vistoria, B.O., Fotos de Avarias, Orçamentos e Comprovantes.
* Visualização rápida em tela cheia e download direto.

---

## 9. Busca Global & Central de Notificações
* **Busca Global (`Ctrl + K` / `Cmd + K`):** Localiza registros em tempo real nos 4 domínios (Leads, Segurados, Apólices e Sinistros), com busca inteligente por CPF/CNPJ com ou sem máscara.
* **Sino de Alertas no Topbar:** Notificações operacionais automáticas sobre apólices a vencer, parcelas em atraso, renovações pendentes e novos leads.

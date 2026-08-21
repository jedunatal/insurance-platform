<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificado de Seguro - Apólice {{ $policy->policy_number }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Barra de Ações Web (oculta na impressão/PDF) */
        .no-print {
            background-color: #0f172a;
            color: #f8fafc;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #334155;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background-color: #295384;
            color: #ffffff;
        }
        .btn-secondary {
            background-color: #334155;
            color: #ffffff;
        }

        /* Estrutura do Documento */
        .document-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Cabeçalho */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #295384;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .brand-logo {
            font-size: 16pt;
            font-weight: bold;
            color: #295384;
            letter-spacing: -0.5px;
        }
        .brand-sub {
            font-size: 8pt;
            font-weight: bold;
            color: #B99B6C;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .doc-title {
            text-align: right;
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-subtitle {
            text-align: right;
            font-size: 8pt;
            color: #64748b;
        }

        /* Seções e Caixas */
        .section-header {
            background-color: #295384;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 8px;
            border-radius: 3px 3px 0 0;
            margin-top: 10px;
            margin-bottom: 0;
        }

        .box-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            border-top: none;
            margin-bottom: 8px;
        }
        .box-table td {
            padding: 5px 8px;
            vertical-align: top;
            border: 1px solid #e2e8f0;
            font-size: 8pt;
        }

        .label {
            font-size: 6.5pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            display: block;
            margin-bottom: 2px;
        }
        .value {
            font-size: 8pt;
            font-weight: 600;
            color: #0f172a;
        }
        .value-highlight {
            font-size: 9pt;
            font-weight: bold;
            color: #295384;
        }

        /* Tabelas de Dados (Coberturas, Parcelas) */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            border-top: none;
            margin-bottom: 8px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .data-table td {
            padding: 4.5px 8px;
            border: 1px solid #e2e8f0;
            font-size: 7.5pt;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }

        /* Totais Financeiros */
        .financial-summary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 10px;
        }
        .financial-summary td {
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            text-align: center;
        }
        .financial-summary .total-card {
            background-color: #295384;
            color: #ffffff;
        }
        .financial-summary .total-card .label {
            color: #e2e8f0;
        }
        .financial-summary .total-card .value {
            color: #ffffff;
            font-size: 11pt;
            font-weight: bold;
        }

        /* Rodapé Jurídico e Assinaturas */
        .legal-notice {
            font-size: 6.5pt;
            color: #64748b;
            text-align: justify;
            line-height: 1.3;
            margin-top: 8px;
            padding: 6px 8px;
            border: 1px dashed #cbd5e1;
            background-color: #fafafa;
        }

        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
        }
        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 20px;
        }
        .signature-line {
            border-top: 1px solid #475569;
            padding-top: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            color: #1e293b;
        }
        .signature-sub {
            font-size: 6.5pt;
            color: #64748b;
        }

        .auth-footer {
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
            font-size: 6.5pt;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    {{-- Barra Superior Interativa para visualização em Navegador --}}
    @if(!isset($isPdf) || !$isPdf)
        <div class="no-print">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-weight: bold; color: #ffffff; font-size: 14px;">Documento Oficial de Apólice</span>
                <span style="font-size: 12px; color: #94a3b8;">#{{ $policy->policy_number }}</span>
            </div>
            <div style="display: flex; gap: 8px;">
                <button onclick="window.print()" class="btn btn-secondary">
                    🖨️ Imprimir
                </button>
                <a href="{{ route('policies.document.pdf', $policy) }}" target="_blank" class="btn btn-secondary">
                    👁️ Ver PDF
                </a>
                <a href="{{ route('policies.document.download', $policy) }}" class="btn btn-primary">
                    ⬇️ Baixar PDF Oficial
                </a>
            </div>
        </div>
    @endif

    <div class="document-container" style="padding: 10px 0;">

        {{-- 1. CABEÇALHO DO DOCUMENTO --}}
        <table class="header-table">
            <tr>
                <td style="width: 50%; vertical-align: middle;">
                    <div class="brand-logo">SALUT ROYALE</div>
                    <div class="brand-sub">Salut Royale Corretora de Seguros</div>
                </td>
                <td style="width: 50%; vertical-align: middle;">
                    <div class="doc-title">Certificado de Apólice</div>
                    <div class="doc-subtitle">
                        Comprovante de Contratação & Condições Particulares
                    </div>
                </td>
            </tr>
        </table>

        {{-- 2. DADOS DE IDENTIFICAÇÃO DO CONTRATO --}}
        <div class="section-header">1. Identificação do Contrato de Seguro</div>
        <table class="box-table">
            <tr>
                <td style="width: 25%;">
                    <span class="label">Número da Apólice</span>
                    <span class="value value-highlight">{{ $policy->policy_number }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">Número da Proposta</span>
                    <span class="value">{{ $policy->proposal_number ?? 'Não informado' }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">Seguradora Emissora</span>
                    <span class="value">{{ $policy->insurer ?? 'Seguradora Parceira' }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">Ramo do Seguro</span>
                    <span class="value">{{ $policy->branch ?? 'Geral' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Data de Emissão</span>
                    <span class="value">{{ $policy->created_at ? $policy->created_at->format('d/m/Y H:i') : now()->format('d/m/Y') }}</span>
                </td>
                <td colspan="2">
                    <span class="label">Período de Vigência</span>
                    <span class="value">
                        Das 24:00h de <strong>{{ $policy->start_date ? $policy->start_date->format('d/m/Y') : 'A definir' }}</strong>
                        até as 24:00h de <strong>{{ $policy->end_date ? $policy->end_date->format('d/m/Y') : 'A definir' }}</strong>
                    </span>
                </td>
                <td>
                    <span class="label">Status Contratual</span>
                    <span class="value" style="color: {{ $policy->status?->value === 'active' || $policy->status === 'Ativa' ? '#16a34a' : '#295384' }};">
                        {{ $policy->status instanceof \App\Enums\PolicyStatusEnum ? $policy->status->getLabel() : ucfirst((string)$policy->status) }}
                    </span>
                </td>
            </tr>
        </table>

        {{-- 3. DADOS CADASTRAIS DO SEGURADO (TITULAR) --}}
        <div class="section-header">2. Dados Cadastrais do Segurado (Titular)</div>
        <table class="box-table">
            <tr>
                <td style="width: 50%;">
                    <span class="label">Nome Completo / Razão Social</span>
                    <span class="value">{{ $policy->insured?->name ?? 'Não informado' }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">CPF / CNPJ</span>
                    <span class="value">{{ $policy->insured?->document ?? 'Não informado' }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">Data Nasc. / Fundação</span>
                    <span class="value">{{ $policy->insured?->birth_date ? $policy->insured->birth_date->format('d/m/Y') : '-' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">E-mail de Contato</span>
                    <span class="value">{{ $policy->insured?->email ?? '-' }}</span>
                </td>
                <td>
                    <span class="label">Telefone / WhatsApp</span>
                    <span class="value">{{ $policy->insured?->phone ?? '-' }}</span>
                </td>
                <td>
                    <span class="label">Tipo de Pessoa</span>
                    <span class="value">{{ $policy->insured?->person_type?->getLabel() ?? 'Pessoa Física' }}</span>
                </td>
            </tr>
            @if($policy->insured?->address || $policy->insured?->city)
                <tr>
                    <td colspan="3">
                        <span class="label">Endereço de Risco / Cobrança</span>
                        <span class="value">
                            {{ $policy->insured->address ?? '' }}
                            @if($policy->insured->number), nº {{ $policy->insured->number }} @endif
                            @if($policy->insured->complement) ({{ $policy->insured->complement }}) @endif
                            @if($policy->insured->neighborhood) - {{ $policy->insured->neighborhood }} @endif
                            @if($policy->insured->city) - {{ $policy->insured->city }}/{{ $policy->insured->state }} @endif
                            @if($policy->insured->zip_code) - CEP: {{ $policy->insured->zip_code }} @endif
                        </span>
                    </td>
                </tr>
            @endif
        </table>

        {{-- 4. DISCRIMINAÇÃO DE COBERTURAS E LIMITES (LMI) --}}
        <div class="section-header">3. Coberturas Contratadas e Limites Máximos de Indenização (LMI)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Garantia / Cobertura Contratada</th>
                    <th style="width: 25%; text-align: right;">Limite Máximo (LMI)</th>
                    <th style="width: 25%; text-align: right;">Franquia Aplicável</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $coverages = is_array($policy->coverages) ? $policy->coverages : json_decode($policy->coverages ?? '[]', true);
                @endphp
                @forelse($coverages ?? [] as $cov)
                    <tr>
                        <td><strong>{{ $cov['name'] ?? 'Cobertura Contratada' }}</strong></td>
                        <td class="text-right font-bold">{{ $cov['limit_amount'] ?? '100% FIPE / Contratual' }}</td>
                        <td class="text-right">{{ $cov['deductible'] ?? 'Isenta / Padrão' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td><strong>Cobertura Básica Compreensiva do Ramo</strong></td>
                        <td class="text-right font-bold">100% FIPE / LMI da Apólice</td>
                        <td class="text-right">{{ $policy->deductible_amount > 0 ? $policy->formattedDeductibleAmount() : 'Conforme Condições Gerais' }}</td>
                    </tr>
                @endforelse
                @if($policy->deductible_amount > 0)
                    <tr style="background-color: #f1f5f9; font-weight: bold;">
                        <td colspan="2">Franquia Principal Estipulada</td>
                        <td class="text-right">{{ $policy->formattedDeductibleAmount() }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- 5. DEMONSTRATIVO FISCAL, FINANCEIRO E TRIBUTÁRIO --}}
        <div class="section-header">4. Demonstrativo Financeiro, Tributário e Composição do Prêmio</div>
        <table class="financial-summary">
            <tr>
                <td style="width: 25%;">
                    <span class="label">Prêmio Líquido</span>
                    <span class="value">R$ {{ number_format((float) $policy->net_premium, 2, ',', '.') }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">Alíquota IOF</span>
                    <span class="value">{{ number_format((float) ($policy->iof_rate ?? 7.38), 2, ',', '.') }}%</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">Valor do IOF</span>
                    <span class="value">R$ {{ number_format((float) $policy->iof_amount, 2, ',', '.') }}</span>
                </td>
                <td style="width: 25%;" class="total-card">
                    <span class="label">Prêmio Total (BRL)</span>
                    <span class="value">R$ {{ number_format((float) $policy->total_premium, 2, ',', '.') }}</span>
                </td>
            </tr>
        </table>

        {{-- 6. GRADE DE PARCELAS E VENCIMENTOS --}}
        <div class="section-header">5. Forma de Pagamento e Grade de Parcelas</div>
        <table class="box-table" style="margin-bottom: 4px;">
            <tr>
                <td style="width: 50%;">
                    <span class="label">Forma de Pagamento</span>
                    <span class="value">
                        {{ $policy->payment_method instanceof \App\Enums\PaymentMethodEnum || $policy->payment_method instanceof \App\Enums\PolicyPaymentMethodEnum ? $policy->payment_method->getLabel() : ucfirst((string)$policy->payment_method) }}
                    </span>
                </td>
                <td style="width: 50%;">
                    <span class="label">Plano de Parcelamento</span>
                    <span class="value">{{ $policy->installments_count ?? 1 }}x parcela(s)</span>
                </td>
            </tr>
        </table>

        @if($policy->installments && $policy->installments->isNotEmpty())
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 15%; text-align: center;">Parcela</th>
                        <th style="width: 30%; text-align: center;">Data de Vencimento</th>
                        <th style="width: 30%; text-align: right;">Valor da Parcela</th>
                        <th style="width: 25%; text-align: center;">Situação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($policy->installments as $inst)
                        <tr>
                            <td class="text-center"><strong>{{ $inst->formattedInstallment() }}</strong></td>
                            <td class="text-center">{{ $inst->due_date ? $inst->due_date->format('d/m/Y') : '-' }}</td>
                            <td class="text-right font-bold">{{ $inst->formattedGrossAmount() }}</td>
                            <td class="text-center">
                                <span style="font-size: 7pt; font-weight: bold; color: {{ $inst->isPaid() ? '#16a34a' : '#d97706' }};">
                                    {{ $inst->status instanceof \App\Enums\FinancialStatusEnum ? $inst->status->getLabel() : ucfirst((string)$inst->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- 7. NOTAS JURÍDICAS E DECLARAÇÃO SUSEP --}}
        <div class="legal-notice">
            <strong>Declaração de Conformidade & Disposições Gerais:</strong> O presente certificado atesta a contratação do seguro especificado, estando as coberturas e garantias sujeitas às Condições Gerais e Especiais da apólice emitidas pela seguradora e devidamente registradas na Superintendência de Seguros Privados (SUSEP). O registro deste plano na SUSEP não implica, por parte da Autarquia, incentivo ou recomendação à sua comercialização. Em caso de sinistro ou divergência, contate imediatamente a central de atendimento ou o corretor credenciado.
        </div>

        {{-- 8. ASSINATURAS DO CORRETOR E EMISSÃO --}}
        <table class="signatures-table">
            <tr>
                <td>
                    <div class="signature-line">
                        {{ $policy->broker?->name ?? 'Corretor de Seguros Autorizado' }}
                    </div>
                    <div class="signature-sub">
                        Corretor Responsável • Registro SUSEP: {{ $policy->broker_id ? 'BR-' . str_pad((string)$policy->broker_id, 6, '0', STR_PAD_LEFT) : 'Ativo / Habilitado' }}
                    </div>
                </td>
                <td>
                    <div class="signature-line">
                        {{ $policy->insured?->name ?? 'Segurado / Contratante' }}
                    </div>
                    <div class="signature-sub">
                        Assinatura do Segurado / Aceite Eletrônico
                    </div>
                </td>
            </tr>
        </table>

        {{-- 9. HASH DE VALIDAÇÃO E TIMESTAMP --}}
        @php
            $authHash = strtoupper(hash('sha256', $policy->id . '-' . $policy->policy_number . '-' . $policy->created_at));
            $shortHash = substr($authHash, 0, 8) . '-' . substr($authHash, 8, 8) . '-' . substr($authHash, 16, 8) . '-' . substr($authHash, 24, 8);
        @endphp
        <div class="auth-footer">
            <span>Autenticação Digital: <strong>{{ $shortHash }}</strong></span>
            <span>Documento emitido em {{ now()->format('d/m/Y \à\s H:i:s') }} via Salut Royale Corretora de Seguros</span>
        </div>

    </div>

</body>
</html>

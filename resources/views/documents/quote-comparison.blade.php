<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Estudo Comparativo de Cotação - {{ $quote->quote_number }}</title>

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
            font-size: 8.5pt;
            line-height: 1.35;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

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
        }
        .brand-sub {
            font-size: 8pt;
            font-weight: bold;
            color: #B99B6C;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .doc-title {
            text-align: right;
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .doc-subtitle {
            text-align: right;
            font-size: 8pt;
            color: #64748b;
        }

        .section-header {
            background-color: #295384;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 8px;
            border-radius: 3px 3px 0 0;
            margin-top: 10px;
        }

        .box-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            border-top: none;
            margin-bottom: 10px;
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

        /* Tabela Comparativa Multi-Seguradoras */
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 12px;
        }
        .comparison-table th {
            background-color: #295384;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #1e3a5f;
            text-align: center;
        }
        .comparison-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            font-size: 7.5pt;
        }
        .comparison-table .feature-col {
            background-color: #f8fafc;
            font-weight: bold;
            color: #334155;
            width: 25%;
        }
        .comparison-table .recommended-col {
            background-color: #f0fdf4;
            border-left: 2px solid #16a34a;
            border-right: 2px solid #16a34a;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .price-highlight {
            font-size: 11pt;
            font-weight: bold;
            color: #295384;
        }
        .price-recommended {
            font-size: 12pt;
            font-weight: bold;
            color: #16a34a;
        }

        .badge-rec {
            background-color: #16a34a;
            color: #ffffff;
            font-size: 6.5pt;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            text-transform: uppercase;
            display: inline-block;
            margin-top: 2px;
        }

        .legal-footer {
            font-size: 6.5pt;
            color: #64748b;
            text-align: justify;
            margin-top: 12px;
            padding: 6px 8px;
            border: 1px dashed #cbd5e1;
            background-color: #fafafa;
        }
    </style>
</head>
<body>

    {{-- 1. CABEÇALHO --}}
    <table class="header-table">
        <tr>
            <td style="width: 50%; vertical-align: middle;">
                <div class="brand-logo">INSURANCE PLATFORM</div>
                <div class="brand-sub">Estudo Comparativo de Cotação</div>
            </td>
            <td style="width: 50%; vertical-align: middle;">
                <div class="doc-title">Proposta de Seguro</div>
                <div class="doc-subtitle">
                    Cotação #{{ $quote->quote_number }} • Validade: {{ $quote->valid_until ? $quote->valid_until->format('d/m/Y') : '15 dias' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- 2. DADOS DO CLIENTE E DO RISCO --}}
    <div class="section-header">1. Dados do Segurado & Objeto do Risco</div>
    <table class="box-table">
        <tr>
            <td style="width: 50%;">
                <span class="label">Proponente / Segurado</span>
                <span class="value">{{ $quote->insured?->name ?? ($quote->lead?->name ?? $quote->title) }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Ramo do Seguro</span>
                <span class="value">{{ $quote->branch?->getLabel() ?? $quote->branch }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Data do Estudo</span>
                <span class="value">{{ $quote->created_at ? $quote->created_at->format('d/m/Y') : now()->format('d/m/Y') }}</span>
            </td>
        </tr>
        @if($quote->notes)
            <tr>
                <td colspan="3">
                    <span class="label">Observações e Perfil</span>
                    <span class="value">{{ $quote->notes }}</span>
                </td>
            </tr>
        @endif
    </table>

    {{-- 3. TABELA COMPARATIVA MULTI-SEGURADORAS --}}
    <div class="section-header">2. Comparativo Detalhado de Propostas</div>
    
    @php
        $options = $quote->options;
        $optCount = max(1, $options->count());
        $colWidth = floor(75 / $optCount) . '%';
    @endphp

    <table class="comparison-table">
        <thead>
            <tr>
                <th class="feature-col">Garantias / Condições</th>
                @foreach($options as $opt)
                    <th style="width: {{ $colWidth }};" class="{{ $opt->is_recommended ? 'recommended-col' : '' }}">
                        {{ $opt->insurer }}
                        @if($opt->is_recommended)
                            <br><span class="badge-rec">Melhor Custo x Benefício</span>
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{-- Prêmio Total --}}
            <tr>
                <td class="feature-col">Prêmio Total (Anual)</td>
                @foreach($options as $opt)
                    <td class="text-center {{ $opt->is_recommended ? 'recommended-col' : '' }}">
                        <span class="{{ $opt->is_recommended ? 'price-recommended' : 'price-highlight' }}">
                            {{ $opt->formattedTotalPremium() }}
                        </span>
                        @if($opt->payment_conditions)
                            <br><span style="font-size: 6.5pt; color: #64748b;">{{ $opt->payment_conditions }}</span>
                        @endif
                    </td>
                @endforeach
            </tr>

            {{-- Franquia --}}
            <tr>
                <td class="feature-col">Franquia Estipulada</td>
                @foreach($options as $opt)
                    <td class="text-center {{ $opt->is_recommended ? 'recommended-col' : '' }}">
                        <strong>{{ $opt->formattedDeductibleAmount() }}</strong>
                        <span style="font-size: 6.5pt; color: #64748b;">({{ ucfirst($opt->deductible_type) }})</span>
                    </td>
                @endforeach
            </tr>

            {{-- Danos a Terceiros (Materiais) --}}
            <tr>
                <td class="feature-col">RCF-V Danos Materiais</td>
                @foreach($options as $opt)
                    <td class="text-center {{ $opt->is_recommended ? 'recommended-col' : '' }}">
                        {{ $opt->third_party_materials > 0 ? 'R$ ' . number_format((float) $opt->third_party_materials, 2, ',', '.') : 'Incluso no LMI' }}
                    </td>
                @endforeach
            </tr>

            {{-- Danos a Terceiros (Corporais) --}}
            <tr>
                <td class="feature-col">RCF-V Danos Corporais</td>
                @foreach($options as $opt)
                    <td class="text-center {{ $opt->is_recommended ? 'recommended-col' : '' }}">
                        {{ $opt->third_party_corporal > 0 ? 'R$ ' . number_format((float) $opt->third_party_corporal, 2, ',', '.') : 'Incluso no LMI' }}
                    </td>
                @endforeach
            </tr>

            {{-- Carro Reserva --}}
            <tr>
                <td class="feature-col">Carro Reserva</td>
                @foreach($options as $opt)
                    <td class="text-center {{ $opt->is_recommended ? 'recommended-col' : '' }}">
                        {{ $opt->car_rental ?? 'Padrão da Seguradora' }}
                    </td>
                @endforeach
            </tr>

            {{-- Vidros e Faróis --}}
            <tr>
                <td class="feature-col">Cobertura de Vidros / Faróis</td>
                @foreach($options as $opt)
                    <td class="text-center {{ $opt->is_recommended ? 'recommended-col' : '' }}">
                        {{ $opt->glass_coverage ?? 'Completa' }}
                    </td>
                @endforeach
            </tr>

            {{-- Destaques e Diferenciais --}}
            <tr>
                <td class="feature-col">Diferenciais da Opção</td>
                @foreach($options as $opt)
                    <td class="{{ $opt->is_recommended ? 'recommended-col' : '' }}" style="font-size: 7pt;">
                        {{ $opt->highlights ?? $opt->notes ?? 'Assistência 24h completa, guincho com km estendido.' }}
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    {{-- 4. INSTRUÇÕES PARA APROVAÇÃO --}}
    <div class="legal-footer">
        <strong>Condições e Validade:</strong> As condições e valores apresentados neste estudo estão sujeitos à análise prévia de risco e aceitação por parte das seguradoras, bem como à confirmação da vistoria prévia e questionário de avaliação do perfil. Para contratar a opção desejada, basta informar ao seu corretor responsável qual das alternativas atende melhor sua necessidade.
    </div>

    <div style="margin-top: 20px; text-align: center; font-size: 7.5pt; color: #475569;">
        Documento gerado em {{ now()->format('d/m/Y H:i') }} • InsurancePlatform Corretora de Seguros
    </div>

</body>
</html>

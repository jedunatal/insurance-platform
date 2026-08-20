<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PolicyDocumentController extends Controller
{
    /**
     * Exibe a versão HTML oficial do Certificado de Apólice no navegador,
     * pronta para visualização em tela cheia ou impressão direta via window.print().
     */
    public function show(Policy $policy): View
    {
        $policy->loadMissing(['insured', 'product', 'broker', 'installments']);

        return view('documents.policy-certificate', [
            'policy' => $policy,
            'isPdf'  => false,
        ]);
    }

    /**
     * Renderiza e exibe o PDF da Apólice em modo stream (inline) no navegador.
     */
    public function streamPdf(Policy $policy): Response
    {
        $policy->loadMissing(['insured', 'product', 'broker', 'installments']);

        $pdf = Pdf::loadView('documents.policy-certificate', [
            'policy' => $policy,
            'isPdf'  => true,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'DejaVu Sans',
        ]);

        $filename = "Apolice_{$policy->policy_number}.pdf";

        return $pdf->stream($filename);
    }

    /**
     * Força o download do arquivo PDF da Apólice com nomenclatura padronizada.
     */
    public function downloadPdf(Policy $policy): Response
    {
        $policy->loadMissing(['insured', 'product', 'broker', 'installments']);

        $pdf = Pdf::loadView('documents.policy-certificate', [
            'policy' => $policy,
            'isPdf'  => true,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'DejaVu Sans',
        ]);

        $insuredSlug = Str::slug($policy->insured?->name ?? 'segurado');
        $filename = "Apolice_{$policy->policy_number}_{$insuredSlug}.pdf";

        return $pdf->download($filename);
    }
}

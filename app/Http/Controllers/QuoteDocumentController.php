<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class QuoteDocumentController extends Controller
{
    /**
     * Visualização em tela/impressão web do comparativo de cotação.
     */
    public function show(Quote $quote): View
    {
        $quote->loadMissing(['options', 'lead', 'insured']);

        return view('documents.quote-comparison', [
            'quote' => $quote,
            'isPdf' => false,
        ]);
    }

    /**
     * Download do PDF oficial da proposta comparativa.
     */
    public function downloadPdf(Quote $quote): Response
    {
        $quote->loadMissing(['options', 'lead', 'insured']);

        $pdf = Pdf::loadView('documents.quote-comparison', [
            'quote' => $quote,
            'isPdf' => true,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'DejaVu Sans',
        ]);

        $clientName = Str::slug($quote->insured?->name ?? ($quote->lead?->name ?? 'proposta'));
        $filename = "Cotacao_{$quote->quote_number}_{$clientName}.pdf";

        return $pdf->download($filename);
    }
}

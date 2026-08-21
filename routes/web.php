<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PolicyDocumentController;
use App\Http\Controllers\QuoteDocumentController;
use App\Livewire\Auth\RegisterBrokerage;
use App\Livewire\Claim;
use App\Livewire\Dashboard;
use App\Livewire\Financial;
use App\Livewire\Insured;
use App\Livewire\Lead;
use App\Livewire\Policy;
use App\Livewire\Quote;
use App\Livewire\Renewal;
use App\Livewire\Settings\TeamManager;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas & Autenticação
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Auto-Cadastro de Corretoras (Multi-Tenant)
    Route::get('/register', RegisterBrokerage::class)->name('register');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Rotas Protegidas do Sistema Operacional (Requer Autenticação)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard Principal
    Route::get('/', Dashboard\Overview::class)->name('dashboard');

    // Clientes em Potencial (Leads)
    Route::prefix('leads')->name('leads.')->group(function () {
        Route::get('/', Lead\ListAll::class)->name('index');
        Route::get('/create', Lead\Create::class)->name('create');
        Route::get('/{record}', Lead\View::class)->name('view');
        Route::get('/{record}/edit', Lead\Edit::class)->name('edit');
    });

    // Cotações & Comparativo Multi-Seguradoras (Quotes)
    Route::prefix('quotes')->name('quotes.')->group(function () {
        Route::get('/', Quote\ListAll::class)->name('index');
        Route::get('/create', Quote\Create::class)->name('create');
        Route::get('/{quote}/document', [QuoteDocumentController::class, 'show'])->name('document.view')->middleware('throttle:30,1');
        Route::get('/{quote}/document/download', [QuoteDocumentController::class, 'downloadPdf'])->name('document.download')->middleware('throttle:30,1');
        Route::get('/{record}', Quote\View::class)->name('view');
    });

    // Segurados (Insureds)
    Route::prefix('insureds')->name('insureds.')->group(function () {
        Route::get('/', Insured\ListAll::class)->name('index');
        Route::get('/create', Insured\Create::class)->name('create');
        Route::get('/{record}', Insured\View::class)->name('view');
        Route::get('/{record}/edit', Insured\Edit::class)->name('edit');
    });

    // Apólices (Policies)
    Route::prefix('policies')->name('policies.')->group(function () {
        Route::get('/', Policy\ListAll::class)->name('index');
        Route::get('/create', Policy\Create::class)->name('create');
        Route::get('/{policy}/document', [PolicyDocumentController::class, 'show'])->name('document.view')->middleware('throttle:30,1');
        Route::get('/{policy}/document/pdf', [PolicyDocumentController::class, 'streamPdf'])->name('document.pdf')->middleware('throttle:30,1');
        Route::get('/{policy}/document/download', [PolicyDocumentController::class, 'downloadPdf'])->name('document.download')->middleware('throttle:30,1');
        Route::get('/{record}', Policy\View::class)->name('view');
        Route::get('/{record}/edit', Policy\Edit::class)->name('edit');
    });

    // Esteira de Renovações (Renewals)
    Route::prefix('renewals')->name('renewals.')->group(function () {
        Route::get('/', Renewal\Pipeline::class)->name('index');
    });

    // Sinistros (Claims)
    Route::prefix('claims')->name('claims.')->group(function () {
        Route::get('/', Claim\ListAll::class)->name('index');
        Route::get('/create', Claim\Create::class)->name('create');
        Route::get('/{record}', Claim\View::class)->name('view');
        Route::get('/{record}/edit', Claim\Edit::class)->name('edit');
    });

    // Gestão Financeira & Comissões (Financial)
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/', Financial\ListInstallments::class)->name('index');
    });

    // Gestão Segura de Documentos Sensíveis (Armazenamento Privado)
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/{document}/preview', [DocumentController::class, 'preview'])->name('preview')->middleware('throttle:30,1');
        Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download')->middleware('throttle:30,1');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
    });

    // Configurações & Gestão da Equipe (RBAC)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', fn () => redirect()->route('settings.team'))->name('index');
        Route::get('/team', TeamManager::class)->name('team');
    });
});
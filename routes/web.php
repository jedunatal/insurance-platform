<?php

use App\Livewire\Claim;
use App\Livewire\Dashboard;
use App\Livewire\Insured;
use App\Livewire\Lead;
use App\Livewire\Policy;
use Illuminate\Support\Facades\Route;

// Dashboard Principal
Route::get('/', Dashboard\Overview::class)->name('dashboard');

// Clientes em Potencial (Leads)
Route::prefix('leads')->name('leads.')->group(function () {
    Route::get('/', Lead\ListAll::class)->name('index');
    Route::get('/create', Lead\Create::class)->name('create');
    Route::get('/{record}', Lead\View::class)->name('view');
    Route::get('/{record}/edit', Lead\Edit::class)->name('edit');
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
    Route::get('/{record}', Policy\View::class)->name('view');
    Route::get('/{record}/edit', Policy\Edit::class)->name('edit');
});

// Sinistros (Claims)
Route::prefix('claims')->name('claims.')->group(function () {
    Route::get('/', Claim\ListAll::class)->name('index');
    Route::get('/create', Claim\Create::class)->name('create');
    Route::get('/{record}', Claim\View::class)->name('view');
    Route::get('/{record}/edit', Claim\Edit::class)->name('edit');
});
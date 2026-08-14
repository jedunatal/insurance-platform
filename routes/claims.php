<?php

use App\Livewire\Claim\Create;
use App\Livewire\Claim\Edit;
use App\Livewire\Claim\ListAll;
use App\Livewire\Claim\View; // Importe quando criar o componente
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'claims',
    'as'     => 'claims.',
    // 'middleware' => ['auth'],
], function () {

    Route::get('/', ListAll::class)->name('index');

    Route::get('/novo', Create::class)->name('create');

    Route::get('/{record}', View::class)->name('view'); // Implemente o componente Show quando necessário

    Route::get('/{record}/editar', Edit::class)->name('edit');
});
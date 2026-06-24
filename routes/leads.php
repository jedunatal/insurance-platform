<?php

use App\Livewire\Lead\Create;
use App\Livewire\Lead\Edit;
use App\Livewire\Lead\ListAll;
use App\Models\Lead;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'leads',
    'as' => 'leads.',
   // 'middleware' => ['auth'],
], function () {
    
    Route::get('/', ListAll::class)
        ->name('index');

    Route::get('/novo', Create::class)
        ->name('create');

    Route::get('/{record}/editar', Edit::class)
        ->name('edit');
});
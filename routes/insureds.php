<?php

use App\Livewire\Insured\Create;
use App\Livewire\Insured\Edit;
use App\Livewire\Insured\ListAll;
use App\Livewire\Insured\View;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'insureds',
    'as'     => 'insureds.',
], function () {
    Route::get('/', ListAll::class)->name('index');
    Route::get('/novo', Create::class)->name('create');
    Route::get('/{record}', View::class)->name('view');
    Route::get('/{record}/editar', Edit::class)->name('edit');
});
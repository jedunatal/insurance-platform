<?php

use App\Livewire\Policy\Create;
use App\Livewire\Policy\Edit;
use App\Livewire\Policy\ListAll;
use App\Livewire\Policy\View;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'policies',
    'as'     => 'policies.',
   //'middleware' => ['auth'],
], function () {
    Route::get('/', ListAll::class)->name('index');
    Route::get('/novo', Create::class)->name('create');
    Route::get('/{record}', View::class)->name('view');
    Route::get('/{record}/editar', Edit::class)->name('edit');
});
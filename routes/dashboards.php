<?php

use App\Livewire\Dashboard\Overview;
use Illuminate\Support\Facades\Route;

Route::get('/', Overview::class)->name('dashboard');
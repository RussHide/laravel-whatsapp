<?php

use App\Http\Controllers\ContactController;
use App\Http\Livewire\ChatComponent;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->resource('contacts', ContactController::class)->except(['show']);


Route::middleware('auth')->get('chat', ChatComponent::class)->name('chat.index');


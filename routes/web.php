<?php

use App\Http\Livewire\Importer\Import;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', \App\Http\Livewire\Dashboard::class)->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
});


Route::get('/import', Import::class);
Route::get('/data-sources', \App\Http\Livewire\DataSources\Index::class);
Route::get('/data-sources/create', \App\Http\Livewire\DataSources\Create::class)->name('data-sources.create');
Route::get('/data-sources/{source}', \App\Http\Livewire\DataSources\Show::class)->name('data-sources.show');
Route::get('/data-sources/{source}/parse', \App\Http\Livewire\DataSources\Parse::class)->name('data-sources.parse');


Route::get('/models', \App\Http\Livewire\Models\Index::class)->name('models.index');
Route::get('/models/create', \App\Http\Livewire\Models\Create::class)->name('models.create');

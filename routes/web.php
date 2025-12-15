<?php

use App\Http\Controllers\UmrohController;

Route::get('/umroh', [UmrohController::class, 'index'])->name('umroh.index');
Route::get('/umroh/create', [UmrohController::class, 'create'])->name('umroh.create');
Route::post('/umroh', [UmrohController::class, 'store'])->name('umroh.store');
Route::get('/umroh/{id}/edit', [UmrohController::class, 'edit'])->name('umroh.edit');
Route::put('/umroh/{id}', [UmrohController::class, 'update'])->name('umroh.update');
Route::delete('/umroh/{id}', [UmrohController::class, 'destroy'])->name('umroh.destroy');

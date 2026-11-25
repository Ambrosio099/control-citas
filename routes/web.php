<?php

use App\Http\Controllers\CitaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function (Request $request) {
    $citas = $request->user()->citas()->with('servicio')
        ->where('fecha_hora', '>=', now())
        ->orderBy('fecha_hora')
        ->get();

    return Inertia::render('Dashboard', [
        'total' => $citas->count(),
        'proximas' => $citas->take(5)->map(fn ($c) => [
            'id' => $c->id,
            'fecha' => $c->fecha_hora->format('d/m/Y H:i'),
            'servicio' => $c->servicio->nombre,
            'estado' => $c->estado,
        ])->values(),
    ]);
})->name('dashboard');

Route::resource('citas', CitaController::class)
    ->only(['index', 'create', 'store', 'show', 'destroy']);

});

require __DIR__.'/settings.php';
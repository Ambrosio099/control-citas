<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class CitaController extends Controller
{
    public function index(Request $request): Response
    {
        $citas = $request->user()->citas()->with('servicio')
            ->orderBy('fecha_hora')->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'fecha' => $c->fecha_hora->format('d/m/Y H:i'),
                'estado' => $c->estado,
                'servicio' => $c->servicio->nombre,
                'notas' => $c->notas,
            ]);

        return Inertia::render('citas/Index', ['citas' => $citas]);
    }

    public function create(): Response
    {
        return Inertia::render('citas/Create', [
            'servicios' => Servicio::orderBy('nombre')->get(['id', 'nombre', 'duracion_minutos']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'servicio_id' => ['required', 'exists:servicios,id'],
            'fecha_hora' => ['required', 'date', 'after:now', 'unique:citas,fecha_hora'],
            'notas' => ['nullable', 'string', 'max:500'],
        ], [
            'fecha_hora.after' => 'La cita debe ser en una fecha futura.',
            'fecha_hora.unique' => 'Ya hay una cita a esa hora.',
        ]);

        $hora = Carbon::parse($datos['fecha_hora'])->hour;
        if ($hora < 9 || $hora >= 18) {
            return back()->withErrors(['fecha_hora' => 'El horario de atención es de 9:00 a 18:00.']);
        }

        $request->user()->citas()->create($datos);

        return redirect('/citas');
    }

    public function show(Request $request, Cita $cita): Response
    {
        abort_unless($cita->user_id === $request->user()->id, 403);

        $cita->load('servicio');

        return Inertia::render('citas/Show', [
            'cita' => [
                'id' => $cita->id,
                'fecha' => $cita->fecha_hora->format('d/m/Y H:i'),
                'estado' => $cita->estado,
                'servicio' => $cita->servicio->nombre,
                'duracion' => $cita->servicio->duracion_minutos,
                'precio' => $cita->servicio->precio,
                'notas' => $cita->notas,
            ],
        ]);
    }

    public function destroy(Request $request, Cita $cita): RedirectResponse
    {
        abort_unless($cita->user_id === $request->user()->id, 403);
        $cita->delete();

        return back();
    }
}

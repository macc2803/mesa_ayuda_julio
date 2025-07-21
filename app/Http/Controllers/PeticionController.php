<?php

namespace App\Http\Controllers;

use App\Models\Peticion;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Notifications\EstadoPeticionActualizado;
use App\Notifications\NuevaPeticionAdm;
use Illuminate\Support\Facades\Notification;

class PeticionController extends Controller
{
    // Muestra las peticiones del encargado
    public function index()
    {
        $user = auth()->user();

        // Verificar que el usuario es un encargado
        if ($user->role !== 'encargado') {
            return redirect()->route('dashboard');
        }

        // Obtener las peticiones de la misma área del encargado
        $peticiones = Peticion::with(['user', 'area'])
            ->where('area_id', $user->area_id)
            ->latest()
            ->get();

        // Obtener estadísticas de las peticiones
        $stats = [
            'total' => Peticion::where('area_id', $user->area_id)->count(),
            'pendientes' => Peticion::where('area_id', $user->area_id)
                ->where('estado', 'enviado')->count(),
            'enProceso' => Peticion::where('area_id', $user->area_id)
                ->where('estado', 'proceso')->count(),
            'completadas' => Peticion::where('area_id', $user->area_id)
                ->where('estado', 'completado')->count(),
        ];

        return Inertia::render('Peticiones/Index', [
            'peticiones' => $peticiones,
            'stats' => $stats,
            'auth' => [
                'user' => $user->only('id', 'name', 'role', 'area_id'),
            ],
            'flash' => session()->only(['success', 'error']),
        ]);
    }

    // Muestra las peticiones del usuario autenticado
    public function misPeticiones()
    {
        $user = auth()->user();
        $peticiones = $user->peticiones()
            ->with('area')
            ->latest()
            ->get();

        // Obtener estadísticas de las peticiones del usuario
        $stats = [
            'total' => $user->peticiones()->count(),
            'pendientes' => $user->peticiones()
                ->where('estado', 'enviado')->count(),
            'enProceso' => $user->peticiones()
                ->where('estado', 'proceso')->count(),
            'completadas' => $user->peticiones()
                ->where('estado', 'completado')->count(),
        ];

        return Inertia::render('Peticiones/MisPeticiones', [
            'peticiones' => $peticiones,
            'stats' => $stats,
            'flash' => session()->only(['success', 'error']),
        ]);
    }

    // Muestra el dashboard con estadísticas
    public function dashboard()
    {
        $user = auth()->user();
        $stats = [];

        // Estadísticas según el rol del usuario
        if ($user->role === 'encargado') {
            $stats = [
                'pendientes' => Peticion::where('area_id', $user->area_id)
                    ->where('estado', 'enviado')->count(),
                'enProceso' => Peticion::where('area_id', $user->area_id)
                    ->where('estado', 'proceso')->count(),
                'completadas' => Peticion::where('area_id', $user->area_id)
                    ->where('estado', 'completado')->count(),
            ];
        } else {
            $stats = [
                'pendientes' => $user->peticiones()->where('estado', 'enviado')->count(),
                'enProceso' => $user->peticiones()->where('estado', 'proceso')->count(),
                'completadas' => $user->peticiones()->where('estado', 'completado')->count(),
            ];
        }

        return Inertia::render('Dashboard', [
            'pendientes' => $stats['pendientes'],
            'enProceso' => $stats['enProceso'],
            'completadas' => $stats['completadas'],
            'flash' => session()->only(['success', 'error']),
        ]);
    }

    // Formulario de creación de peticiones
    public function create()
    {
        return Inertia::render('Peticiones/Create', [
            'areas' => Area::all(),
            'flash' => session()->only(['success', 'error']),
        ]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'titulo' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'area_id' => 'required|exists:areas,id',
    ]);

    // Crear la nueva petición
    $peticion = $request->user()->peticiones()->create([
        'titulo' => $validated['titulo'],
        'descripcion' => $validated['descripcion'],
        'estado' => 'enviado',
        'area_id' => $validated['area_id'],
    ]);

    // Obtener los encargados del área
    $area = Area::with('users')->find($validated['area_id']);
    $encargados = $area->users()->where('role', 'encargado')->get();

    // Enviar la notificación usando la clase correcta
    Notification::send($encargados, new NuevaPeticionAdm($peticion));

    return redirect()->route('dashboard')
        ->with('success', 'Petición enviada con éxito. Se ha notificado al área correspondiente.');
}


    // Actualiza el estado de la petición y notifica al usuario
    public function update(Request $request, Peticion $peticion)
    {
        $request->validate([
            'estado' => 'required|in:enviado,proceso,completado',
        ]);

        // Verifica si el encargado tiene permisos para modificar esta petición
        if (
            auth()->user()->role === 'encargado' &&
            auth()->user()->area_id !== $peticion->area_id
        ) {
            return redirect()->back()->with('error', 'No tienes permisos para esta acción.');
        }

        // Actualiza el estado de la petición
        $peticion->update(['estado' => $request->estado]);

        // Notificar al usuario que creó la petición
        $peticion->user->notify(new EstadoPeticionActualizado($peticion));

        return redirect()->back()
            ->with('success', 'Estado actualizado correctamente. Se ha notificado al usuario.');
    }
}

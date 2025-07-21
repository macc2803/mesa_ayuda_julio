<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class DashboardController extends Controller
{
    // Método para cargar el dashboard y las notificaciones del usuario
    public function dashboard()
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Obtener las notificaciones no leídas del usuario
        $notificaciones = $user->unreadNotifications;

        // Pasar las notificaciones a la vista
        return view('dashboard', compact('notificaciones'));
    }
}


<?php

namespace App\Notifications;

use App\Models\Peticion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NuevaPeticionCreada extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Peticion $peticion
    ) {}

    public function via($notifiable)
    {
        return ['mail', 'database']; // Enviar por ambos canales
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("📌 Nueva Petición - {$this->peticion->titulo}")
            ->greeting("¡Hola {$notifiable->name}!")
            ->line('Se ha asignado una nueva petición a tu área:')
            ->line(new HtmlString(
                "<strong>Título:</strong> {$this->peticion->titulo}<br>".
                "<strong>Descripción:</strong> {$this->peticion->descripcion}"
            ))
            ->action('Ver Detalles', route('peticiones.show', $this->peticion->id))
            ->line('Por favor revisa y atiende esta solicitud.')
            ->salutation('Saludos, '.config('app.name'));
    }

    public function toArray($notifiable)
    {
        return [
            'peticion_id' => $this->peticion->id,
            'titulo' => $this->peticion->titulo,
            'area_id' => $this->peticion->area_id,
            'fecha' => $this->peticion->created_at->format('d/m/Y H:i'),
            'mensaje' => 'Nueva petición asignada a tu área',
            'link' => route('peticiones.show', $this->peticion->id),
            'icono' => '📋' // Emoji para la interfaz
        ];
    }
}
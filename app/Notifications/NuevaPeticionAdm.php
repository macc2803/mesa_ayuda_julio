<?php

namespace App\Notifications;

use App\Models\Peticion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NuevaPeticionAdm extends Notification implements ShouldQueue
{
    use Queueable;

    public $peticion;

    public function __construct(Peticion $peticion)
    {
        $this->peticion = $peticion;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nueva petición recibida')
            ->greeting('Hola ' . $notifiable->name)
            ->line('Has recibido una nueva petición en tu área:')
            ->line('Título: ' . $this->peticion->titulo)
            ->line('Descripción: ' . $this->peticion->descripcion)
            ->line('Enviada por: ' . $this->peticion->user->name)
            ->action('Ver peticiones', url('/peticiones'))
            ->line('Gracias por tu trabajo.');
    }

    public function toArray($notifiable)
    {
        return [
            'peticion_id' => $this->peticion->id,
        ];
    }
}

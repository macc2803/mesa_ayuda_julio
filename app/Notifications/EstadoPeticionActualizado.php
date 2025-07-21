<?php
namespace App\Notifications;

use App\Models\Peticion;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EstadoPeticionActualizado extends Notification
{
    public $peticion;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Peticion $peticion
     */
    public function __construct(Peticion $peticion)
    {
        // Asignar la propiedad peticion
        $this->peticion = $peticion;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];  // Puedes agregar otros canales si es necesario (por ejemplo, base de datos)
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Estado de la Petición ')
                    ->greeting('Hola ' . $notifiable->name)
                    ->line('El estado de la petición "' . $this->peticion->titulo . '" ha sido actualizado a: ' . $this->peticion->estado)
                    ->action('Ver Petición', url('/peticiones/' . $this->peticion->id))
                    ->line('Gracias por mesa de Ayuda.');
    }


    public function toDatabase($notifiable)
    {
        // Aquí definimos los datos que serán almacenados en la tabla de notificaciones
        return [
            'peticion_id' => $this->peticion->id,
            'titulo' => $this->peticion->titulo,
            'estado' => $this->peticion->estado,
            'mensaje' => "El estado de tu petición '{$this->peticion->titulo}' ha cambiado a {$this->peticion->estado}.",
        ];
    }
}

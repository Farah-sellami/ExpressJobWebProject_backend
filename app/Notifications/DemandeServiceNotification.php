<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DemandeService;
use Illuminate\Notifications\Messages\DatabaseMessage;


class DemandeServiceNotification extends Notification
{
    use Queueable;
    protected $demandeService;

    /**
     * Create a new notification instance.
     */
    public function __construct(DemandeService $demandeService)
    {
        $this->demandeService = $demandeService;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject('Nouvelle demande de service')
        ->line('Un client a fait une demande de service.')
        ->action('Consulter les détails', url('/demandes/'.$this->demandeService->id))
        ->line('Merci de votre attention.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

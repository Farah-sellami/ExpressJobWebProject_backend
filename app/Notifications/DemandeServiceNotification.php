<?php

namespace App\Notifications;

use App\Models\DemandeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DemandeServiceNotification extends Notification
{
    use Queueable;

    public $demande;

    /**
     * Crée une nouvelle instance de notification.
     */
    public function __construct(DemandeService $demande)
    {
        $this->demande = $demande;
    }

    /**
     * Canaux de notification.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Contenu de l'e-mail.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle demande de service')
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Vous avez reçu une nouvelle demande de service.")
            ->line("Date de la demande : " . $this->demande->DateDemande)
            ->line("Client ID : " . $this->demande->client_id)
            ->action('Voir la demande', url('/')) // Tu peux remplacer par un vrai lien vers ta page front
            ->line('Merci d’utiliser Express Job !');
    }

    /**
     * Représentation en tableau (optionnel, utile si tu veux les stocker aussi).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'client_id' => $this->demande->client_id,
            'professionnel_id' => $this->demande->professionnel_id,
            'date_demande' => $this->demande->DateDemande,
        ];
    }
}

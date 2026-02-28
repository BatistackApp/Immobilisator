<?php

namespace App\Notifications;

use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssetDepreciatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Asset $asset) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Immobilisation totalement amortie : {$this->asset->reference}")
            ->line("L'immobilisation {$this->asset->designation} est arrivée au terme de son amortissement.")
            ->action('Voir l\'actif', url("/admin/assets/{$this->asset->id}"))
            ->line('Pensez à traiter sa sortie si le bien n\'est plus utilisé.');
    }

    public function toArray($notifiable): array
    {
        return [
            'asset_id' => $this->asset->id,
            'reference' => $this->asset->reference,
            'message' => 'Amortissement terminé',
        ];
    }
}

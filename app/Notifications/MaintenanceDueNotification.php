<?php

namespace App\Notifications;

use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Asset $asset, protected string $dueDate) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Alerte Maintenance Préventive : {$this->asset->reference}")
            ->greeting('Bonjour,')
            ->line("Une maintenance préventive est à prévoir pour l'immobilisation : {$this->asset->designation}.")
            ->line("Date d'échéance prévue : ".\Carbon\Carbon::parse($this->dueDate)->format('d/m/Y'))
            ->action('Voir l\'immobilisation', url("/admin/assets/{$this->asset->id}"))
            ->line('Le respect des cycles de maintenance garantit la longévité de vos actifs.');
    }

    public function toArray($notifiable): array
    {
        return [
            'asset_id' => $this->asset->id,
            'reference' => $this->asset->reference,
            'due_date' => $this->dueDate,
            'message' => 'Maintenance préventive à prévoir',
            'type' => 'preventive_maintenance',
        ];
    }
}

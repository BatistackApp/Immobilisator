<?php

namespace App\Notifications;

use App\Models\Leasing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeasingExpiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Leasing $leasing) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Échéance de contrat de crédit-bail : {$this->leasing->contract_number}")
            ->greeting('Bonjour,')
            ->line("Le contrat de crédit-bail n°{$this->leasing->contract_number} arrive à échéance prochainement.")
            ->line("Détails de l'immobilisation : {$this->leasing->asset->designation} ({$this->leasing->asset->reference})")
            ->line('Date de fin de contrat : '.$this->leasing->end_date->format('d/m/Y'))
            ->line("Montant de l'option d'achat : ".number_format($this->leasing->purchase_option_price, 2, ',', ' ').' €')
            ->action('Gérer le leasing', url("/admin/assets/{$this->leasing->asset_id}"))
            ->line("Pensez à lever l'option d'achat ou à restituer le bien avant la date limite.");
    }

    public function toArray($notifiable): array
    {
        return [
            'leasing_id' => $this->leasing->id,
            'contract_number' => $this->leasing->contract_number,
            'asset_id' => $this->leasing->asset_id,
            'asset_reference' => $this->leasing->asset->reference,
            'asset_designation' => $this->leasing->asset->designation,
            'expiry_date' => $this->leasing->end_date->toDateString(),
            'purchase_option' => $this->leasing->purchase_option_price,
            'message' => 'Fin de contrat de leasing proche',
            'type' => 'leasing_expiry',
        ];
    }
}

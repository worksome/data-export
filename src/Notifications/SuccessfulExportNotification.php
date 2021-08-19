<?php

namespace Worksome\DataExport\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Worksome\DataExport\Models\Export;

class SuccessfulExportNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Export $export
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Your export is ready.')
            ->line(sprintf('Type: %s', $this->export->type))
            ->line(sprintf('Size: %s', $this->export->getFormattedSize()))
            ->action('Download Export', Storage::url($this->export->path));
    }
}

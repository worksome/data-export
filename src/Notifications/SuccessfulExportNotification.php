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
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your data export is ready for you'))
            ->line(__('Hi there'))
            ->line(__('Your :exportType is ready to download now.', ['type' => $this->export->type]))
            ->action(__('Download export'), Storage::url($this->export->path));
    }
}

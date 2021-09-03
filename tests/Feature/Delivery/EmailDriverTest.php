<?php

namespace Worksome\DataExport\Tests\Feature\Delivery;

use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Worksome\DataExport\Delivery\EmailDriver;
use Worksome\DataExport\Notifications\SuccessfulExportNotification;
use Worksome\DataExport\Tests\Factories\ExportFactory;
use Worksome\DataExport\Tests\Factories\UserFactory;

it('can process the delivery with the email driver', function () {
    Notification::fake();

    $user = UserFactory::new()->create();

    $export = ExportFactory::new()->make([
        'deliveries' => [
            ['type' => 'email', 'value' => $user->email],
        ],
    ]);

    $driver = new EmailDriver();
    $driver->deliver($export);

    Notification::assertSentTo(
        new AnonymousNotifiable(),
        SuccessfulExportNotification::class,
        function ($notification, $channels, $notifiable) use ($user) {
            return $notifiable->routes['mail'] === $user->email;
        });
});

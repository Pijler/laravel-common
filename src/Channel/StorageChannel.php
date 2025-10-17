<?php

namespace Common\Channel;

use Closure;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class StorageChannel
{
    /**
     * The message instance.
     */
    private ?MailMessage $message = null;

    /**
     * The callback that should be used to get storage path.
     */
    public static ?Closure $storagePathCallback = null;

    /**
     * Set a callback that should be used to create storage path.
     */
    public static function storagePathUsing($callback): void
    {
        static::$storagePathCallback = $callback;
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification): void
    {
        $this->message = $notification->toMail($notifiable);

        $this->saveInDatabase($notifiable, $notification);

        Storage::put($this->getPath($notification), $this->message->render());
    }

    /**
     * Send the given notification.
     */
    protected function saveInDatabase($notifiable, Notification $notification): mixed
    {
        return $notifiable->routeNotificationFor('database', $notification)->updateOrCreate([
            'id' => $notification->id,
        ], $this->buildPayload($notification));
    }

    /**
     * Build an array payload for the DatabaseNotification Model.
     */
    protected function buildPayload(Notification $notification): array
    {
        return [
            'type' => get_class($notification),
            'data' => [
                'subject' => $this->message->subject,
                'path' => $this->getPath($notification),
            ],
        ];
    }

    /**
     * Get the path for the notification.
     */
    protected function getPath(Notification $notification): string
    {
        if (static::$storagePathCallback) {
            return call_user_func(static::$storagePathCallback, $notification);
        }

        $date = date('Y-m-d');

        $environment = App::environment();

        return "/notifications/{$environment}/{$date}/{$notification->id}.html";
    }
}

<?php

namespace Common\Traits;

trait NotificationUrl
{
    /**
     * The url to the notification.
     */
    private ?string $url = null;

    /**
     * Get the URL for the notification.
     */
    abstract private function getURL(object $notifiable): string;

    /**
     * Get the cached URL for the notification.
     */
    private function getCachedURL(object $notifiable): string
    {
        if (filled($this->url)) {
            return $this->url;
        }

        $url = call_user_func([$this, 'getURL'], $notifiable);

        return tap($url, fn ($url) => $this->url = $url);
    }
}

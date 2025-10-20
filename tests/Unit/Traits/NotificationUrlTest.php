<?php

use Common\Traits\NotificationUrl;

beforeEach(function () {
    $this->class = new class
    {
        use NotificationUrl;

        /**
         * Number of times getURL is called
         */
        public int $calls = 0;

        /**
         * Generate a notification URL for the given notifiable entity.
         */
        private function getURL(object $notifiable): string
        {
            $this->calls++;

            return "https://example.com/notify/{$notifiable->id}";
        }

        /**
         * Call the getCachedURL method
         */
        public function callGetCachedURL(object $notifiable): string
        {
            return $this->getCachedURL($notifiable);
        }
    };
});

it('should generate url on first call', function () {
    $url = $this->class->callGetCachedURL((object) ['id' => 123]);

    expect($this->class->calls)->toBe(1);
    expect($url)->toBe('https://example.com/notify/123');
});

it('should return cached url on subsequent calls', function () {
    $url1 = $this->class->callGetCachedURL((object) ['id' => 456]);

    $url2 = $this->class->callGetCachedURL((object) ['id' => 999]);

    expect($this->class->calls)->toBe(1);
    expect($url1)->toBe('https://example.com/notify/456');
    expect($url2)->toBe('https://example.com/notify/456');
});

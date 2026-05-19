<?php

use Common\Channel\StorageChannel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Notification;

test('it should return the notifiable when storageRelation is not defined', function () {
    $channel = new StorageChannel;

    $notifiable = new class {};
    $notification = new class extends Notification {};

    $resolved = $this->invokeMethod($channel, 'resolveRelation', [$notifiable, $notification]);

    expect($resolved)->toBe($notifiable);
});

test('it should resolve relation from storageRelation on the notification', function () {
    $channel = new StorageChannel;

    $relation = Mockery::mock(Relation::class);

    $notifiable = new class {};

    $notification = new class($relation) extends Notification
    {
        public function __construct(private Relation $storageRelation) {}

        public function storageRelation($notifiable): Relation
        {
            return $this->storageRelation;
        }
    };

    $resolved = $this->invokeMethod($channel, 'resolveRelation', [$notifiable, $notification]);

    expect($resolved)->toBe($relation);
});

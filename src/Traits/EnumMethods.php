<?php

namespace Common\Traits;

use Illuminate\Support\Str;

trait EnumMethods
{
    /**
     * Get the random enum case.
     */
    public static function random(): self
    {
        return collect(self::cases())->random();
    }

    /**
     * Get the random enum key.
     */
    public static function randomKey(): mixed
    {
        return collect(self::keys())->random();
    }

    /**
     * Get the random enum value.
     */
    public static function randomValue(): mixed
    {
        return collect(self::values())->random();
    }

    /**
     * Get all keys of the enum.
     */
    public static function keys(): array
    {
        return collect(self::cases())->pluck('name')->toArray();
    }

    /**
     * Get all values of the enum.
     */
    public static function values(): array
    {
        return collect(self::cases())->pluck('value')->toArray();
    }

    /**
     * Get the translatable name of the enum.
     */
    public function trans(): string
    {
        $class = self::slug();

        return trans("enum.{$class}.{$this->value}");
    }

    /**
     * Get the enum class slug.
     */
    public static function slug(): string
    {
        $class = class_basename(self::class);

        return Str::snake(Str::before($class, 'Enum'), '-');
    }

    /**
     * Check if this enum case is the same as the given one.
     */
    public function is(mixed $enum): bool
    {
        $enum = self::get($enum);

        return $this->value === $enum;
    }

    /**
     * Check if this enum case is one of the given ones.
     */
    public function in(array $enums): bool
    {
        $enums = collect($enums)->transform(fn ($enum) => self::get($enum));

        return $enums->contains($this->value);
    }

    /**
     * Get the enum case by the given key.
     */
    public static function fromKey(mixed $key): ?self
    {
        return collect(self::cases())->first(function ($case) use ($key) {
            return data_get($case, 'name') === $key;
        });
    }

    /**
     * Get the enum case by the given value.
     */
    public static function fromValue(mixed $value): ?self
    {
        return collect(self::cases())->first(function ($case) use ($value) {
            return data_get($case, 'value') === $value;
        });
    }

    /**
     * Check if the given key exists in the enum.
     */
    public static function existKey(mixed $key): bool
    {
        if (is_null($key)) {
            return true;
        }

        return in_array($key, self::keys(), true);
    }

    /**
     * Check if the given value exists in the enum.
     */
    public static function existValue(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        return in_array($value, self::values(), true);
    }

    /**
     * Get the enum case by the given case or value.
     */
    public static function find(mixed $enum): ?self
    {
        $value = self::get($enum);

        return self::fromValue($value);
    }

    /**
     * Get the enum value by the given case or value.
     */
    public static function get(mixed $enum): mixed
    {
        $value = $enum instanceof self ? $enum->value : $enum;

        return self::existValue($value) ? $value : null;
    }
}

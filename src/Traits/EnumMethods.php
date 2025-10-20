<?php

namespace Common\Traits;

use Illuminate\Support\Str;

use function Illuminate\Support\enum_value;

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
     * Check if this enum case is not the same as the given one.
     */
    public function isNot(mixed $enum): bool
    {
        return ! $this->is($enum);
    }

    /**
     * Check if this enum case is the same as the given one.
     */
    public function is(mixed $enum): bool
    {
        $enum = enum_value($enum);

        return $this->value === $enum;
    }

    /**
     * Check if this enum case is not one of the given ones.
     */
    public function notIn(array $enums): bool
    {
        return ! $this->in($enums);
    }

    /**
     * Check if this enum case is one of the given ones.
     */
    public function in(array $enums): bool
    {
        $enums = collect($enums)->transform(fn ($enum) => enum_value($enum));

        return $enums->contains($this->value);
    }

    /**
     * Get the enum case by the given key.
     */
    public static function fromKey(mixed $key, mixed $default = null): ?self
    {
        return collect(self::cases())->first(function ($case) use ($key) {
            return data_get($case, 'name') === $key;
        }, $default);
    }

    /**
     * Get the enum case by the given value.
     */
    public static function fromValue(mixed $value, mixed $default = null): ?self
    {
        return collect(self::cases())->first(function ($case) use ($value) {
            return data_get($case, 'value') === $value;
        }, $default);
    }

    /**
     * Check if the given key exists in the enum.
     */
    public static function existKey(mixed $key): bool
    {
        return in_array($key, self::keys(), true);
    }

    /**
     * Check if the given value exists in the enum.
     */
    public static function existValue(mixed $value): bool
    {
        return in_array($value, self::values(), true);
    }
}

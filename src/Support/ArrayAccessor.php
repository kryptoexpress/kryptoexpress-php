<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Support;

use KryptoExpress\SDK\Error\SDKError;

final class ArrayAccessor
{
    /**
     * @param array<string, mixed> $data
     */
    public static function string(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        if (!is_string($value) || $value === '') {
            throw new SDKError(sprintf('Expected non-empty string key "%s".', $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function nullableString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function int(array $data, string $key): int
    {
        $value = $data[$key] ?? null;

        if (!is_int($value)) {
            throw new SDKError(sprintf('Expected integer key "%s".', $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function nullableInt(array $data, string $key): ?int
    {
        $value = $data[$key] ?? null;

        return is_int($value) ? $value : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function bool(array $data, string $key): bool
    {
        $value = $data[$key] ?? null;

        if (!is_bool($value)) {
            throw new SDKError(sprintf('Expected boolean key "%s".', $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function nullableFloat(array $data, string $key): ?float
    {
        $value = $data[$key] ?? null;

        return is_int($value) || is_float($value) ? (float) $value : null;
    }

    /**
     * @param array<string, mixed> $data
     * @return list<string>
     */
    public static function stringList(array $data, string $key): array
    {
        $value = $data[$key] ?? null;

        if (!is_array($value)) {
            throw new SDKError(sprintf('Expected list key "%s".', $key));
        }

        $result = [];

        foreach ($value as $item) {
            if (!is_string($item)) {
                throw new SDKError(sprintf('Expected string items in list key "%s".', $key));
            }

            $result[] = $item;
        }

        return $result;
    }
}

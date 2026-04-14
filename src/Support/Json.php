<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Support;

use JsonException;
use KryptoExpress\SDK\Error\SDKError;

final class Json
{
    /**
     * @param array<string, mixed> $payload
     */
    public static function encode(array $payload): string
    {
        try {
            return json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        } catch (JsonException $exception) {
            throw new SDKError('Unable to encode request payload to JSON.', 0, $exception);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function decodeObject(string $payload): array
    {
        if ($payload === '') {
            return [];
        }

        try {
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new SDKError('Unable to decode API response JSON.', 0, $exception);
        }

        if (!is_array($decoded)) {
            throw new SDKError('Expected JSON object or array in API response.');
        }

        return $decoded;
    }

    public static function compact(string $payload): string
    {
        $decoded = self::decodeObject($payload);

        try {
            return json_encode($decoded, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);
        } catch (JsonException $exception) {
            throw new SDKError('Unable to normalize JSON payload.', 0, $exception);
        }
    }
}

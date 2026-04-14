<?php

declare(strict_types=1);

namespace KryptoExpress\SDK\Support;

use KryptoExpress\SDK\Error\APIError;
use KryptoExpress\SDK\Error\AuthError;
use KryptoExpress\SDK\Error\NotFoundError;
use KryptoExpress\SDK\Error\RateLimitError;
use KryptoExpress\SDK\Error\ValidationError;

final class ErrorMapper
{
    /**
     * @param array<string, mixed> $payload
     */
    public static function map(int $statusCode, array $payload = []): APIError
    {
        $message = is_string($payload['message'] ?? null)
            ? $payload['message']
            : (is_string($payload['error'] ?? null) ? (string) $payload['error'] : 'KryptoExpress API request failed.');

        $errorCode = is_string($payload['error'] ?? null) ? $payload['error'] : null;
        $retryAfter = is_int($payload['retryAfter'] ?? null) ? $payload['retryAfter'] : null;
        $details = [];

        if (is_array($payload['details'] ?? null)) {
            foreach ($payload['details'] as $detail) {
                if (
                    is_array($detail)
                    && is_string($detail['field'] ?? null)
                    && is_string($detail['message'] ?? null)
                ) {
                    $details[] = [
                        'field' => $detail['field'],
                        'message' => $detail['message'],
                    ];
                }
            }
        }

        return match ($statusCode) {
            400, 422 => new ValidationError($message, $statusCode, $errorCode, $retryAfter, $details),
            401, 403 => new AuthError($message, $statusCode, $errorCode, $retryAfter, $details),
            404 => new NotFoundError($message, $statusCode, $errorCode, $retryAfter, $details),
            429 => new RateLimitError($message, $statusCode, $errorCode, $retryAfter, $details),
            default => new APIError($message, $statusCode, $errorCode, $retryAfter, $details),
        };
    }
}

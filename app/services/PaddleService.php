<?php

declare(strict_types=1);

namespace App\Services;

class PaddleService
{
    private string $secretKey;

    public function __construct(array $config)
    {
        $this->secretKey = $config['secret'];
    }

    public static function parseSignature(string $signature): array
    {
        $keyValues = explode(';', $signature);
        $ts = explode('=', $keyValues[0]);
        $h1 = explode('=', $keyValues[1]);

        return [
            'ts' => $ts[1],
            'h1' => $h1[1],
        ];
    }

    public function verifySignature(string $payload, string $signature): bool
    {
        $parsedSignature = self::parseSignature($signature);
        
        $signedPayload = $parsedSignature['ts'] . ':' . $payload;
        $computedSignature = hash_hmac('sha256', $signedPayload, $this->secretKey);

        return $computedSignature === $signature;
    }
}

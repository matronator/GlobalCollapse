<?php

declare(strict_types=1);

namespace App\Services\Darknet;

class DarknetResponse
{
    public function __construct(public string $message, public string $type = 'info', public ?string $redirect = null)
    {
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'redirect' => $this->redirect,
        ];
    }

    public static function fromArray(array $data): static
    {
        return new static($data['message'], $data['type'], $data['redirect'] ?? null);
    }
}

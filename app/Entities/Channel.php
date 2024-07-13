<?php

namespace App\Entities;

use App\Entities\Providers\BaseProvider;

class Channel
{
    private array $providers = [];

    /**
     * @param string $code
     * @param bool $isEnabled
     */
    public function __construct(private readonly string $code, private readonly bool $isEnabled)
    {
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param BaseProvider $provider
     * @return void
     */
    public function addProvider(BaseProvider $provider): void
    {
        $this->providers[] = $provider;
    }

    public function getProviders(): array
    {
        return $this->providers;
    }
}

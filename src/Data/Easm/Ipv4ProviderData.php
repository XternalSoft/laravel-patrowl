<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * The providers an IPv4 address belongs to.
 */
final class Ipv4ProviderData
{
    public function __construct(
        public ?string $cdn = null,
        public ?string $cloud = null,
        public ?string $saas = null,
        public ?string $waf = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            cdn: $data['cdn'] ?? null,
            cloud: $data['cloud'] ?? null,
            saas: $data['saas'] ?? null,
            waf: $data['waf'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'cdn' => $this->cdn,
            'cloud' => $this->cloud,
            'saas' => $this->saas,
            'waf' => $this->waf,
        ];
    }
}

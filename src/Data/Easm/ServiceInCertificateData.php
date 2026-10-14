<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A service presenting a certificate.
 */
final class ServiceInCertificateData
{
    public function __construct(
        public int $id,
        public ?string $value = null,
        public ?string $assetType = null,
        public ?string $protocol = null,
        public ?int $portNumber = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            value: $data['value'] ?? null,
            assetType: $data['asset_type'] ?? null,
            protocol: $data['protocol'] ?? null,
            portNumber: $data['port_number'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'asset_type' => $this->assetType,
            'protocol' => $this->protocol,
            'port_number' => $this->portNumber,
        ];
    }
}

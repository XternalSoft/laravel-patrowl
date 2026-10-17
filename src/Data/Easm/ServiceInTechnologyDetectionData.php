<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * The service a technology was detected on.
 */
final class ServiceInTechnologyDetectionData
{
    public function __construct(
        public int $id,
        public ?string $name = null,
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
            name: $data['name'] ?? null,
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
            'name' => $this->name,
            'protocol' => $this->protocol,
            'port_number' => $this->portNumber,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * The service a web server runs on.
 */
final class WebServerServiceData
{
    public function __construct(
        public int $id,
        public ?int $portNumber = null,
        public ?string $protocol = null,
        public ?bool $isSsl = null,
        public ?string $state = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            portNumber: $data['port_number'] ?? null,
            protocol: $data['protocol'] ?? null,
            isSsl: $data['is_ssl'] ?? null,
            state: $data['state'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'port_number' => $this->portNumber,
            'protocol' => $this->protocol,
            'is_ssl' => $this->isSsl,
            'state' => $this->state,
        ];
    }
}

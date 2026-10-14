<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A TLS certificate, unique by serial number, presented by one or more services.
 */
final class CertificateData
{
    public function __construct(
        public int $id,
        public ?string $serialNumber = null,
        /** @var array<int, array{id: int, value: string, asset_type: string, protocol: string, port_number: int}> */
        public array $services = [],
        public ?bool $isExpired = null,
        public ?bool $isMismatched = null,
        public ?bool $isSelfsigned = null,
        public ?string $dataText = null,
        public ?string $dataPem = null,
        /** @var array<string, mixed>|null */
        public ?array $dataParsed = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            serialNumber: $data['serial_number'] ?? null,
            services: $data['services'] ?? [],
            isExpired: $data['is_expired'] ?? null,
            isMismatched: $data['is_mismatched'] ?? null,
            isSelfsigned: $data['is_selfsigned'] ?? null,
            dataText: $data['data_text'] ?? null,
            dataPem: $data['data_pem'] ?? null,
            dataParsed: $data['data_parsed'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'serial_number' => $this->serialNumber,
            'services' => $this->services,
            'is_expired' => $this->isExpired,
            'is_mismatched' => $this->isMismatched,
            'is_selfsigned' => $this->isSelfsigned,
            'data_text' => $this->dataText,
            'data_pem' => $this->dataPem,
            'data_parsed' => $this->dataParsed,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}

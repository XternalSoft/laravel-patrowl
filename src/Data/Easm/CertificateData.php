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
        /** @var array<int, ServiceInCertificateData> */
        public array $services = [],
        public ?bool $isExpired = null,
        public ?bool $isMismatched = null,
        public ?bool $isSelfsigned = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?string $dataText = null,
        public ?string $dataPem = null,
        /** @var array<string, mixed>|null */
        public ?array $dataParsed = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            serialNumber: $data['serial_number'] ?? null,
            services: array_map(fn (array $item) => ServiceInCertificateData::fromApi($item), $data['services'] ?? []),
            isExpired: $data['is_expired'] ?? null,
            isMismatched: $data['is_mismatched'] ?? null,
            isSelfsigned: $data['is_selfsigned'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            dataText: $data['data_text'] ?? null,
            dataPem: $data['data_pem'] ?? null,
            dataParsed: $data['data_parsed'] ?? null
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
            'services' => array_map(fn (ServiceInCertificateData $item) => $item->toArray(), $this->services),
            'is_expired' => $this->isExpired,
            'is_mismatched' => $this->isMismatched,
            'is_selfsigned' => $this->isSelfsigned,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'data_text' => $this->dataText,
            'data_pem' => $this->dataPem,
            'data_parsed' => $this->dataParsed,
        ];
    }
}

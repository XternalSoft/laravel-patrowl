<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * The TLS certificate presented by a service.
 */
final class CertificateInServiceData
{
    public function __construct(
        public int $id,
        public ?string $serialNumber = null,
        public ?bool $isExpired = null,
        public ?bool $isMismatched = null,
        public ?bool $isSelfsigned = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            serialNumber: $data['serial_number'] ?? null,
            isExpired: $data['is_expired'] ?? null,
            isMismatched: $data['is_mismatched'] ?? null,
            isSelfsigned: $data['is_selfsigned'] ?? null
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
            'is_expired' => $this->isExpired,
            'is_mismatched' => $this->isMismatched,
            'is_selfsigned' => $this->isSelfsigned,
        ];
    }
}

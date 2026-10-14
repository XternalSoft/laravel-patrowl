<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A DNS record of a domain.
 */
final class DnsRecordData
{
    public function __construct(
        public ?string $recordType = null,
        public ?string $value = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            recordType: $data['record_type'] ?? null,
            value: $data['value'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'record_type' => $this->recordType,
            'value' => $this->value,
        ];
    }
}

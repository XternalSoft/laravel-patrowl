<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A domain (or subdomain) discovered on the attack surface, with WHOIS and DNS data.
 */
final class DomainData
{
    public function __construct(
        public int $id,
        public ?string $value = null,
        public ?int $topDomain = null,
        public ?int $parentDomain = null,
        public ?int $wwwDomain = null,
        public ?string $registrar = null,
        public ?int $registrarId = null,
        /** @var array<int, string> */
        public array $registrarUrl = [],
        /** @var array<int, string> */
        public array $registrarStatus = [],
        /** @var array<int, string> */
        public array $registrantName = [],
        /** @var array<int, string> */
        public array $registrantCountry = [],
        /** @var array<int, string> */
        public array $registrantStateProvince = [],
        /** @var array<int, DnsRecordData> */
        public array $dnsRecords = [],
        /** @var array<int, string> */
        public array $creationDates = [],
        /** @var array<int, string> */
        public array $updatedDates = [],
        /** @var array<int, string> */
        public array $expirationDates = [],
        /** @var array<int, string> */
        public array $nameServers = [],
        public ?string $whoisText = null,
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
            value: $data['value'] ?? null,
            topDomain: $data['top_domain'] ?? null,
            parentDomain: $data['parent_domain'] ?? null,
            wwwDomain: $data['www_domain'] ?? null,
            registrar: $data['registrar'] ?? null,
            registrarId: $data['registrar_id'] ?? null,
            registrarUrl: $data['registrar_url'] ?? [],
            registrarStatus: $data['registrar_status'] ?? [],
            registrantName: $data['registrant_name'] ?? [],
            registrantCountry: $data['registrant_country'] ?? [],
            registrantStateProvince: $data['registrant_state_province'] ?? [],
            dnsRecords: array_map(fn (array $item) => DnsRecordData::fromApi($item), $data['dnsrecords'] ?? []),
            creationDates: $data['creation_dates'] ?? [],
            updatedDates: $data['updated_dates'] ?? [],
            expirationDates: $data['expiration_dates'] ?? [],
            nameServers: $data['name_servers'] ?? [],
            whoisText: $data['whois_text'] ?? null,
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
            'value' => $this->value,
            'top_domain' => $this->topDomain,
            'parent_domain' => $this->parentDomain,
            'www_domain' => $this->wwwDomain,
            'registrar' => $this->registrar,
            'registrar_id' => $this->registrarId,
            'registrar_url' => $this->registrarUrl,
            'registrar_status' => $this->registrarStatus,
            'registrant_name' => $this->registrantName,
            'registrant_country' => $this->registrantCountry,
            'registrant_state_province' => $this->registrantStateProvince,
            'dnsrecords' => array_map(fn (DnsRecordData $item) => $item->toArray(), $this->dnsRecords),
            'creation_dates' => $this->creationDates,
            'updated_dates' => $this->updatedDates,
            'expiration_dates' => $this->expirationDates,
            'name_servers' => $this->nameServers,
            'whois_text' => $this->whoisText,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}

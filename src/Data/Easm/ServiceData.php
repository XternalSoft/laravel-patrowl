<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A network service (open port) detected on a scanned asset. `banner` is only filled on detail.
 */
final class ServiceData
{
    public function __construct(
        public int $id,
        public ?string $value = null,
        public ?string $assetType = null,
        public ?string $name = null,
        public ?string $protocol = null,
        public ?int $portNumber = null,
        public ?string $state = null,
        public ?bool $isSsl = null,
        public ?bool $hasBanner = null,
        /** @var array{id: int, serial_number: string, is_expired: bool, is_mismatched: bool, is_selfsigned: bool}|null */
        public ?array $certificate = null,
        /** @var array<int, array{id: int, url: string, server: string, title: string, path: string, jarm: string}> */
        public array $webservers = [],
        public ?string $lastSeen = null,
        public ?string $banner = null,
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
            assetType: $data['asset_type'] ?? null,
            name: $data['name'] ?? null,
            protocol: $data['protocol'] ?? null,
            portNumber: $data['port_number'] ?? null,
            state: $data['state'] ?? null,
            isSsl: $data['is_ssl'] ?? null,
            hasBanner: $data['has_banner'] ?? null,
            certificate: $data['certificate'] ?? null,
            webservers: $data['webservers'] ?? [],
            lastSeen: $data['last_seen'] ?? null,
            banner: $data['banner'] ?? null,
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
            'asset_type' => $this->assetType,
            'name' => $this->name,
            'protocol' => $this->protocol,
            'port_number' => $this->portNumber,
            'state' => $this->state,
            'is_ssl' => $this->isSsl,
            'has_banner' => $this->hasBanner,
            'certificate' => $this->certificate,
            'webservers' => $this->webservers,
            'last_seen' => $this->lastSeen,
            'banner' => $this->banner,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}

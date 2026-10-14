<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A CVE, as returned by the list.
 */
final class CveData
{
    public function __construct(
        public int $id,
        public ?string $identifier = null,
        public ?string $publishedAt = null,
        public ?string $description = null,
        public ?int $severity = null,
        public ?CvssData $cvss = null,
        public ?ThreatInfoData $threatInfo = null,
        /** @var array<int, TechnologyInCveData> */
        public array $technologies = [],
        /** @var array<int, string> */
        public array $references = []
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            identifier: $data['identifier'] ?? null,
            publishedAt: $data['published_at'] ?? null,
            description: $data['description'] ?? null,
            severity: $data['severity'] ?? null,
            cvss: isset($data['cvss']) ? CvssData::fromApi($data['cvss']) : null,
            threatInfo: isset($data['threat_info']) ? ThreatInfoData::fromApi($data['threat_info']) : null,
            technologies: array_map(fn (array $item) => TechnologyInCveData::fromApi($item), $data['technologies'] ?? []),
            references: $data['references'] ?? []
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
            'published_at' => $this->publishedAt,
            'description' => $this->description,
            'severity' => $this->severity,
            'cvss' => $this->cvss?->toArray(),
            'threat_info' => $this->threatInfo?->toArray(),
            'technologies' => array_map(fn (TechnologyInCveData $item) => $item->toArray(), $this->technologies),
            'references' => $this->references,
        ];
    }
}

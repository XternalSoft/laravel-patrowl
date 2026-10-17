<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A CVE in detail, with per-version CVSS, EPSS and exploits.
 */
final class CveDetailData
{
    public function __construct(
        public int $id,
        public ?string $identifier = null,
        public ?string $cwe = null,
        public ?string $cweName = null,
        /** @var array<int, TechnologyInCveData> */
        public array $technologies = [],
        /** @var array<int, ExploitData> */
        public array $exploits = [],
        public ?string $cvss2Vector = null,
        public ?float $cvss2Score = null,
        public ?string $cvss3Vector = null,
        public ?float $cvss3Score = null,
        public ?string $cvss4Vector = null,
        public ?float $cvss4Score = null,
        public ?float $epssScore = null,
        public ?float $epssPercentile = null,
        public ?int $hearsScore = null,
        public ?int $severity = null,
        public ?string $description = null,
        public ?ThreatInfoData $threatInfo = null,
        public ?string $publishedAt = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
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
            cwe: $data['cwe'] ?? null,
            cweName: $data['cwe_name'] ?? null,
            technologies: array_map(fn (array $item) => TechnologyInCveData::fromApi($item), $data['technologies'] ?? []),
            exploits: array_map(fn (array $item) => ExploitData::fromApi($item), $data['exploits'] ?? []),
            cvss2Vector: $data['cvss2_vector'] ?? null,
            cvss2Score: $data['cvss2_score'] ?? null,
            cvss3Vector: $data['cvss3_vector'] ?? null,
            cvss3Score: $data['cvss3_score'] ?? null,
            cvss4Vector: $data['cvss4_vector'] ?? null,
            cvss4Score: $data['cvss4_score'] ?? null,
            epssScore: $data['epss_score'] ?? null,
            epssPercentile: $data['epss_percentile'] ?? null,
            hearsScore: $data['hears_score'] ?? null,
            severity: $data['severity'] ?? null,
            description: $data['description'] ?? null,
            threatInfo: isset($data['threat_info']) ? ThreatInfoData::fromApi($data['threat_info']) : null,
            publishedAt: $data['published_at'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
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
            'cwe' => $this->cwe,
            'cwe_name' => $this->cweName,
            'technologies' => array_map(fn (TechnologyInCveData $item) => $item->toArray(), $this->technologies),
            'exploits' => array_map(fn (ExploitData $item) => $item->toArray(), $this->exploits),
            'cvss2_vector' => $this->cvss2Vector,
            'cvss2_score' => $this->cvss2Score,
            'cvss3_vector' => $this->cvss3Vector,
            'cvss3_score' => $this->cvss3Score,
            'cvss4_vector' => $this->cvss4Vector,
            'cvss4_score' => $this->cvss4Score,
            'epss_score' => $this->epssScore,
            'epss_percentile' => $this->epssPercentile,
            'hears_score' => $this->hearsScore,
            'severity' => $this->severity,
            'description' => $this->description,
            'threat_info' => $this->threatInfo?->toArray(),
            'published_at' => $this->publishedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'references' => $this->references,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A CVE from the catalog. Fields after `references` are only filled on detail.
 */
final class CveData
{
    public function __construct(
        public int $id,
        public ?string $identifier = null,
        public ?string $publishedAt = null,
        public ?string $description = null,
        /** 0 = info, 1 = low, 2 = medium, 3 = high, 4 = critical */
        public ?int $severity = null,
        /** @var array{score: float, version: int}|null */
        public ?array $cvss = null,
        public ?ThreatInfoData $threatInfo = null,
        /** @var array<int, array{id: int, product: string, version: string, vendor: string, partial_cpe: string}> */
        public array $technologies = [],
        /** @var array<int, string> */
        public array $references = [],
        public ?string $cwe = null,
        public ?string $cweName = null,
        /** @var array<int, array<string, mixed>> */
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
            identifier: $data['identifier'] ?? null,
            publishedAt: $data['published_at'] ?? null,
            description: $data['description'] ?? null,
            severity: $data['severity'] ?? null,
            cvss: $data['cvss'] ?? null,
            threatInfo: isset($data['threat_info']) ? ThreatInfoData::fromApi($data['threat_info']) : null,
            technologies: $data['technologies'] ?? [],
            references: $data['references'] ?? [],
            cwe: $data['cwe'] ?? null,
            cweName: $data['cwe_name'] ?? null,
            exploits: $data['exploits'] ?? [],
            cvss2Vector: $data['cvss2_vector'] ?? null,
            cvss2Score: $data['cvss2_score'] ?? null,
            cvss3Vector: $data['cvss3_vector'] ?? null,
            cvss3Score: $data['cvss3_score'] ?? null,
            cvss4Vector: $data['cvss4_vector'] ?? null,
            cvss4Score: $data['cvss4_score'] ?? null,
            epssScore: $data['epss_score'] ?? null,
            epssPercentile: $data['epss_percentile'] ?? null,
            hearsScore: $data['hears_score'] ?? null,
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
            'identifier' => $this->identifier,
            'published_at' => $this->publishedAt,
            'description' => $this->description,
            'severity' => $this->severity,
            'cvss' => $this->cvss,
            'threat_info' => $this->threatInfo?->toArray(),
            'technologies' => $this->technologies,
            'references' => $this->references,
            'cwe' => $this->cwe,
            'cwe_name' => $this->cweName,
            'exploits' => $this->exploits,
            'cvss2_vector' => $this->cvss2Vector,
            'cvss2_score' => $this->cvss2Score,
            'cvss3_vector' => $this->cvss3Vector,
            'cvss3_score' => $this->cvss3Score,
            'cvss4_vector' => $this->cvss4Vector,
            'cvss4_score' => $this->cvss4Score,
            'epss_score' => $this->epssScore,
            'epss_percentile' => $this->epssPercentile,
            'hears_score' => $this->hearsScore,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}

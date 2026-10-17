<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * The highest available CVSS score of a CVE (v4 > v3 > v2).
 */
final class CvssData
{
    public function __construct(
        public ?float $score = null,
        public ?int $version = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            score: $data['score'] ?? null,
            version: $data['version'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'version' => $this->version,
        ];
    }
}

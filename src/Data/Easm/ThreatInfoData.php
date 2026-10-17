<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * Threat intelligence flags of a CVE.
 */
final class ThreatInfoData
{
    public function __construct(
        public bool $isExploitable = false,
        public bool $isInTheNews = false,
        public bool $isInTheWild = false,
        public bool $isKev = false
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            isExploitable: $data['is_exploitable'] ?? false,
            isInTheNews: $data['is_in_the_news'] ?? false,
            isInTheWild: $data['is_in_the_wild'] ?? false,
            isKev: $data['is_kev'] ?? false
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'is_exploitable' => $this->isExploitable,
            'is_in_the_news' => $this->isInTheNews,
            'is_in_the_wild' => $this->isInTheWild,
            'is_kev' => $this->isKev,
        ];
    }
}

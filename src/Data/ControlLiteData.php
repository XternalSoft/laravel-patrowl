<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

use Xternalsoft\LaravelPatrowl\Enums\ControlResultEnum;
use Xternalsoft\LaravelPatrowl\Enums\ControlStatusEnum;

final class ControlLiteData
{
    /**
     * @param  array<int, string>  $cveIdentifiers
     * @param  array<int, string>  $references
     * @param  array<int, string>  $products
     */
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ControlStatusEnum|string|null $status = null,
        public ?ControlResultEnum $result = null,
        public array $cveIdentifiers = [],
        public ?string $cvssVector = null,
        public ?float $cvssScore = null,
        public string|int|null $severity = null,
        public ?string $description = null,
        public array $references = [],
        public ?string $vendor = null,
        public ?string $product = null,
        public array $products = [],
        public ?string $startedAt = null,
        public ?string $finishedAt = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        $statusRaw = $data['status'] ?? null;
        $status = null;
        if ($statusRaw !== null) {
            $status = is_numeric($statusRaw)
                ? (ControlStatusEnum::tryFrom((int) $statusRaw) ?? (int) $statusRaw)
                : $statusRaw;
        }

        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            status: $status,
            result: isset($data['result']) ? ControlResultEnum::tryFrom((int) $data['result']) : null,
            cveIdentifiers: $data['cve_identifiers'] ?? [],
            cvssVector: $data['cvss_vector'] ?? null,
            cvssScore: isset($data['cvss_score']) ? (float) $data['cvss_score'] : null,
            severity: $data['severity'] ?? null,
            description: $data['description'] ?? null,
            references: $data['references'] ?? [],
            vendor: $data['vendor'] ?? null,
            product: $data['product'] ?? null,
            products: $data['products'] ?? (isset($data['product']) ? [$data['product']] : []),
            startedAt: $data['started_at'] ?? null,
            finishedAt: $data['finished_at'] ?? null,
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
            'title' => $this->title,
            'status' => $this->status instanceof ControlStatusEnum ? $this->status->value : $this->status,
            'result' => $this->result?->value,
            'cve_identifiers' => $this->cveIdentifiers,
            'cvss_vector' => $this->cvssVector,
            'cvss_score' => $this->cvssScore,
            'severity' => $this->severity,
            'description' => $this->description,
            'references' => $this->references,
            'vendor' => $this->vendor,
            'product' => $this->product,
            'products' => $this->products,
            'started_at' => $this->startedAt,
            'finished_at' => $this->finishedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}

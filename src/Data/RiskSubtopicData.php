<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final readonly class RiskSubtopicData
{
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public ?string $description = null,
        public ?bool $isAvailable = null,
        public ?int $defaultSeverity = null,
        public ?int $securityCheck = null,
        public ?string $remediation = null,
        public ?int $remediationEffort = null,
        public ?int $remediationPriority = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'] ?? $data['name'] ?? '',
            slug: $data['slug'] ?? '',
            description: $data['description'] ?? null,
            isAvailable: $data['is_available'] ?? null,
            defaultSeverity: $data['default_severity'] ?? null,
            securityCheck: $data['security_check'] ?? null,
            remediation: $data['remediation'] ?? null,
            remediationEffort: $data['remediation_effort'] ?? null,
            remediationPriority: $data['remediation_priority'] ?? null,
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
            'slug' => $this->slug,
            'description' => $this->description,
            'is_available' => $this->isAvailable,
            'default_severity' => $this->defaultSeverity,
            'security_check' => $this->securityCheck,
            'remediation' => $this->remediation,
            'remediation_effort' => $this->remediationEffort,
            'remediation_priority' => $this->remediationPriority,
        ];
    }
}

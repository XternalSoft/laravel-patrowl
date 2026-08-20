<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class OrganizationFeaturesData
{
    public function __construct(
        public bool $isSso,
        public bool $riskInsightsEnabled,
        public bool $teamsEnabled,
        public bool $technologiesEnabled,
        public bool $typosquattingEnabled,
        public bool $outsideBusinessHoursEnabled,
        public bool $outsideBusinessHours,
        public bool $autoEasm
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            isSso: (bool) ($data['is_sso'] ?? false),
            riskInsightsEnabled: (bool) ($data['risk_insights_enabled'] ?? false),
            teamsEnabled: (bool) ($data['teams_enabled'] ?? false),
            technologiesEnabled: (bool) ($data['technologies_enabled'] ?? false),
            typosquattingEnabled: (bool) ($data['typosquatting_enabled'] ?? false),
            outsideBusinessHoursEnabled: (bool) ($data['outside_business_hours_enabled'] ?? false),
            outsideBusinessHours: (bool) ($data['outside_business_hours'] ?? false),
            autoEasm: (bool) ($data['auto_easm'] ?? false)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'is_sso' => $this->isSso,
            'risk_insights_enabled' => $this->riskInsightsEnabled,
            'teams_enabled' => $this->teamsEnabled,
            'technologies_enabled' => $this->technologiesEnabled,
            'typosquatting_enabled' => $this->typosquattingEnabled,
            'outside_business_hours_enabled' => $this->outsideBusinessHoursEnabled,
            'outside_business_hours' => $this->outsideBusinessHours,
            'auto_easm' => $this->autoEasm,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class OrganizationData
{
    public function __construct(
        public OrganizationIdentityData $identity,
        public OrganizationMembersData $members,
        public OrganizationProfileData $profile,
        public OrganizationFeaturesData $features,
        public OrganizationCreditsData $credits,
        public OrganizationMonitoringData $monitoring
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            identity: OrganizationIdentityData::fromApi((array) ($data['identity'] ?? [])),
            members: OrganizationMembersData::fromApi((array) ($data['members'] ?? [])),
            profile: OrganizationProfileData::fromApi((array) ($data['profile'] ?? [])),
            features: OrganizationFeaturesData::fromApi((array) ($data['features'] ?? [])),
            credits: OrganizationCreditsData::fromApi((array) ($data['credits'] ?? [])),
            monitoring: OrganizationMonitoringData::fromApi((array) ($data['monitoring'] ?? []))
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'identity' => $this->identity->toArray(),
            'members' => $this->members->toArray(),
            'profile' => $this->profile->toArray(),
            'features' => $this->features->toArray(),
            'credits' => $this->credits->toArray(),
            'monitoring' => $this->monitoring->toArray(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class OrganizationCreditsData
{
    public function __construct(
        public CreditsData $easm,
        public CreditsData $pentest,
        public int $greybox
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            easm: CreditsData::fromApi((array) ($data['easm'] ?? [])),
            pentest: CreditsData::fromApi((array) ($data['pentest'] ?? [])),
            greybox: (int) ($data['greybox'] ?? 0)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'easm' => $this->easm->toArray(),
            'pentest' => $this->pentest->toArray(),
            'greybox' => $this->greybox,
        ];
    }
}

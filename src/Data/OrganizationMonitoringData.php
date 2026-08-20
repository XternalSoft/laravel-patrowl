<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class OrganizationMonitoringData
{
    public function __construct(
        public int $pentestedAssets,
        public int $rotationFrequency,
        public ?int $autoEasmActivationConsumption = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            pentestedAssets: (int) ($data['pentested_assets'] ?? 0),
            rotationFrequency: (int) ($data['rotation_frequency'] ?? 0),
            autoEasmActivationConsumption: isset($data['auto_easm_activation_consumption'])
                ? (int) $data['auto_easm_activation_consumption']
                : null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'pentested_assets' => $this->pentestedAssets,
            'rotation_frequency' => $this->rotationFrequency,
            'auto_easm_activation_consumption' => $this->autoEasmActivationConsumption,
        ];
    }
}

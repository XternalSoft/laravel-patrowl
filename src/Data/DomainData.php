<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

/**
 * Represents a Domain DTO, which is structurally identical to an Asset DTO
 * where the type is 'domain'. Inherits from AssetData to avoid duplication.
 *
 * The `type` property is inherited from AssetData and should be set to 'domain'
 * when creating an instance of this DTO.
 */
final class DomainData extends AssetData {}

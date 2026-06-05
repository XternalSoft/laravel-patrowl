<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Concerns;

trait HasOrganizationContext
{
    /**
     * Merge the default organization ID into the parameters if not already set.
     *
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    protected function mergeOrganizationId(array $parameters, ?int $orgId, string $key = 'org_id'): array
    {
        if (! isset($parameters[$key]) && $orgId) {
            $parameters[$key] = $orgId;
        }

        return $parameters;
    }
}

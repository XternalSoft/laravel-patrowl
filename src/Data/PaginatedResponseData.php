<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

/**
 * @template TValue
 */
final class PaginatedResponseData
{
    /** @var TValue[] */
    public array $results;

    public function __construct(
        public int $count,
        public ?string $next,
        public ?string $previous,
        array $results
    ) {
        $this->results = $results;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  class-string<TValue>  $resultsClass
     */
    public static function fromApi(array $data, string $resultsClass): self
    {
        return new self(
            count: $data['count'],
            next: $data['next'] ?? null,
            previous: $data['previous'] ?? null,
            results: array_map(fn (array $item) => $resultsClass::fromApi($item), $data['results'])
        );
    }

    public function toArray(): array
    {
        return [
            'count' => $this->count,
            'next' => $this->next,
            'previous' => $this->previous,
            'results' => array_map(fn ($item) => $item->toArray(), $this->results),
        ];
    }
}

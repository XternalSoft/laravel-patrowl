<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Easm;

use Closure;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Request\CreatesDtoFromResponse;

/**
 * Retrieves a single EASM resource by its ID (or identifier for CVEs).
 *
 * @template TData of object
 */
final class GetEasmRequest extends Request
{
    use CreatesDtoFromResponse;

    protected Method $method = Method::GET;

    /**
     * @param  Closure(array<string, mixed>): TData  $mapper
     */
    public function __construct(
        protected string $endpoint,
        protected int|string $id,
        protected Closure $mapper
    ) {}

    public function resolveEndpoint(): string
    {
        return $this->endpoint.'/'.rawurlencode((string) $this->id);
    }

    /**
     * @return TData
     */
    public function createDtoFromResponse(Response $response): object
    {
        return ($this->mapper)($response->json());
    }
}

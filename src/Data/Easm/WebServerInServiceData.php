<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A web server exposed by a service.
 */
final class WebServerInServiceData
{
    public function __construct(
        public int $id,
        public ?string $url = null,
        public ?string $server = null,
        public ?string $title = null,
        public ?string $path = null,
        public ?string $jarm = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            url: $data['url'] ?? null,
            server: $data['server'] ?? null,
            title: $data['title'] ?? null,
            path: $data['path'] ?? null,
            jarm: $data['jarm'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'server' => $this->server,
            'title' => $this->title,
            'path' => $this->path,
            'jarm' => $this->jarm,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A web server detected on a service exposed by an asset.
 */
final class WebServerData
{
    public function __construct(
        public int $id,
        public ?string $value = null,
        /** @var array{id: int, port_number: int, protocol: string, is_ssl: bool, state: string}|null */
        public ?array $service = null,
        public ?string $url = null,
        public ?string $server = null,
        public ?string $path = null,
        public ?string $title = null,
        public ?float $responseTime = null,
        public ?int $statusCode = null,
        public ?int $contentLength = null,
        public ?string $contentType = null,
        public ?string $jarm = null,
        public ?string $faviconHash = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            value: $data['value'] ?? null,
            service: $data['service'] ?? null,
            url: $data['url'] ?? null,
            server: $data['server'] ?? null,
            path: $data['path'] ?? null,
            title: $data['title'] ?? null,
            responseTime: $data['response_time'] ?? null,
            statusCode: $data['status_code'] ?? null,
            contentLength: $data['content_length'] ?? null,
            contentType: $data['content_type'] ?? null,
            jarm: $data['jarm'] ?? null,
            faviconHash: $data['favicon_hash'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'service' => $this->service,
            'url' => $this->url,
            'server' => $this->server,
            'path' => $this->path,
            'title' => $this->title,
            'response_time' => $this->responseTime,
            'status_code' => $this->statusCode,
            'content_length' => $this->contentLength,
            'content_type' => $this->contentType,
            'jarm' => $this->jarm,
            'favicon_hash' => $this->faviconHash,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}

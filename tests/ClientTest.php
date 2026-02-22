<?php

use Xternalsoft\LaravelPatrowl\Exceptions\MissingApiTokenException;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;

it('throws an exception if the api token is not configured', function () {
    config()->set('patrowl.api_token', null);

    LaravelPatrowl::getAsset(1);
})->throws(MissingApiTokenException::class, 'Patrowl API token is not configured.');

it('can get the default organization id', function () {
    config()->set('patrowl.default_organization_id', 123);

    expect(LaravelPatrowl::getDefaultOrganizationId())->toBe(123);
});


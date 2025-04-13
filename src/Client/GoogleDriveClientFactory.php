<?php

declare(strict_types=1);

namespace Zantolov\Zoogle\Symfony\Client;

use Zantolov\ZoogleCms\Client\BaseGoogleDriveClient;
use Zantolov\ZoogleCms\Client\GoogleDriveClient;

class GoogleDriveClientFactory
{
    public function __construct(
        private BaseGoogleDriveClient $client,
        private CachedGoogleDriveClient $cachedClient
    ) {
    }

    public function create(bool $useCache): GoogleDriveClient
    {
        return $useCache ? $this->cachedClient : $this->client;
    }
}

<?php

declare(strict_types=1);

namespace Zantolov\Zoogle\Symfony;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Zantolov\Zoogle\Cms\Service\GoogleDrive\Client\GoogleDriveAuth;
use Zantolov\Zoogle\Cms\Service\GoogleDrive\Client\GoogleDriveClient;
use Zantolov\Zoogle\Cms\Service\GoogleDrive\Configuration\Configuration;

final class ZoogleCmsExtension extends Extension
{
    /**
     * @param array<mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new ZoogleCmsConfiguration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__));
        $loader->load('../config/services.php');

        $container->getDefinition(GoogleDriveAuth::class)->setArguments([
            '$authConfigPath' => $config['google_api']['auth_file'],
            '$clientId' => $config['google_api']['client_id'],
        ]);

        $container->getDefinition(GoogleDriveClient::class)->setArguments([
            '$useCache' => $config['cache'],
        ]);

        $container->getDefinition(Configuration::class)->setArguments([
            '$rootDirectoryId' => $config['google_api']['google_drive_root_directory'],
        ]);
    }
}

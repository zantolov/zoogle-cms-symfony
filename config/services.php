<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Zantolov\Zoogle\Model\Service\Converting\ContentConverter;
use Zantolov\Zoogle\Model\Service\Converting\Converter;
use Zantolov\Zoogle\Model\Service\Converting\ElementConverter;
use Zantolov\Zoogle\Model\Service\Converting\HeadingConverter;
use Zantolov\Zoogle\Model\Service\Converting\InlineObjectConverter;
use Zantolov\Zoogle\Model\Service\Converting\SubtitleConverter;
use Zantolov\Zoogle\Model\Service\Converting\TitleConverter;
use Zantolov\Zoogle\Model\Service\Processing\DocumentProcessingHub;
use Zantolov\Zoogle\Model\Service\Processing\DocumentProcessor;
use Zantolov\Zoogle\Model\Service\Processing\ListNormalizationProcessor;
use Zantolov\Zoogle\Model\Service\Processing\ObjectNormalizationProcessor;
use Zantolov\Zoogle\Symfony\Client\CachedGoogleDriveClient;
use Zantolov\Zoogle\Symfony\Client\GoogleDriveClientFactory;
use Zantolov\Zoogle\Symfony\Controller\ImageController;
use Zantolov\Zoogle\Symfony\Service\LocalImagePersistenceProcessor;
use Zantolov\Zoogle\Symfony\Twig\ZoogleCmsTwigExtension;
use Zantolov\ZoogleCms\Client\BaseGoogleDriveClient;
use Zantolov\ZoogleCms\Client\GoogleDriveAuth;
use Zantolov\ZoogleCms\Client\GoogleDriveClient;
use Zantolov\ZoogleCms\Configuration\Configuration;
use Zantolov\ZoogleCms\Content\Document\DocumentFactory;
use Zantolov\ZoogleCms\Content\Html\Converting\HtmlConverter;
use Zantolov\ZoogleCms\Content\Html\Processing\HtmlProcessingHub;
use Zantolov\ZoogleCms\Content\Html\Processing\HtmlProcessor;
use Zantolov\ZoogleCms\Content\Html\Processing\QuoteFormattingProcessor;
use Zantolov\ZoogleCms\Content\Html\Processing\YoutubeVideoProcessor;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(HtmlConverter::class);
    $services->set(ZoogleCmsTwigExtension::class);
    $services->set(BaseGoogleDriveClient::class);
    $services->set(GoogleDriveClientFactory::class);
    $services->set(GoogleDriveAuth::class);
    $services->set(Configuration::class);
    $services->set(DocumentFactory::class);

    $services->set(ImageController::class)->tag('controller.service_arguments');

    $services->set(CachedGoogleDriveClient::class)
        ->args([
            '$client' => service(BaseGoogleDriveClient::class),
        ]);

    $services->set(GoogleDriveClient::class)
        ->factory([service(GoogleDriveClientFactory::class), 'create']);

    $services->instanceof(ElementConverter::class)->tag('zoogle_document_converter');
    $services->set(ContentConverter::class);
    $services->set(HeadingConverter::class);
    $services->set(InlineObjectConverter::class);
    $services->set(SubtitleConverter::class);
    $services->set(TitleConverter::class);
    $services->set(Converter::class)
        ->args([
            '$converters' => tagged_iterator('zoogle_document_converter'),
        ]);

    $services->instanceof(DocumentProcessor::class)->tag('zoogle_document_processor');
    $services->set(ListNormalizationProcessor::class);
    $services->set(ObjectNormalizationProcessor::class);
    $services->set(DocumentProcessingHub::class)
        ->args([
            '$processors' => tagged_iterator('zoogle_document_processor'),
        ]);

    $services->instanceof(HtmlProcessor::class)->tag('zoogle_html_processor');
    $services->set(YoutubeVideoProcessor::class);
    $services->set(QuoteFormattingProcessor::class);
    $services->set(LocalImagePersistenceProcessor::class)
        ->arg('$cache', service('zoogle_cache_pool'));

    $services->set(HtmlProcessingHub::class)
        ->args([
            '$processors' => tagged_iterator('zoogle_html_processor'),
        ]);
};

<?php

declare(strict_types=1);

namespace Zantolov\Zoogle\Symfony\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Zantolov\Zoogle\Model\Model\Document\Document;
use Zantolov\Zoogle\Model\Model\Document\DocumentElement;
use Zantolov\Zoogle\Model\Model\Document\Image as ImageElement;
use Zantolov\Zoogle\Model\Service\Processing\AbstractElementDocumentProcessor;

final class LocalImagePersistenceProcessor extends AbstractElementDocumentProcessor
{
    public function __construct(
        private CacheInterface $cache,
        private RouterInterface $router,
        private LoggerInterface $logger,
    ) {
    }

    public function priority(): int
    {
        return 100;
    }

    protected function supports(DocumentElement $element): bool
    {
        return $element instanceof ImageElement;
    }

    protected function processElement(DocumentElement $element, Document $document): DocumentElement
    {
        assert($element instanceof ImageElement);

        $imageHash = sha1($document->id.$element->id);
        $pathParts = pathinfo($element->src);
        $extension = $pathParts['extension'] ?? 'jpg';
        $filename = sprintf('%s.%s', $imageHash, $extension);

        /**
         * @todo add support for different persistence adapters
         */
        // Warm up cache
        $this->logger->debug(
            sprintf(
                'Warming up cache with key %s by fetching image from %s',
                $filename,
                $element->src,
            )
        );
        $this->cache->get($filename, static fn (): string => \Safe\file_get_contents($element->src));

        $proxyedImageUrl = $this->router->generate(
            'zoogle_cms_image',
            ['filename' => $filename],
            RouterInterface::ABSOLUTE_PATH,
        );

        return $element->withSrc($proxyedImageUrl);
    }
}

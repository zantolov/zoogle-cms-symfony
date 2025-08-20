<?php

declare(strict_types=1);

namespace Zantolov\Zoogle\Symfony\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Zantolov\Zoogle\Model\Model\Document\Document;
use Zantolov\Zoogle\Model\Model\Document\DocumentElement;
use Zantolov\ZoogleCms\Content\Document\DocumentFactory;
use Zantolov\ZoogleCms\Content\Html\Converting\HtmlConverter;
use Zantolov\ZoogleCms\Content\Html\Processing\HtmlProcessingHub;

final class ZoogleCmsTwigExtension extends AbstractExtension
{
    public function __construct(
        private DocumentFactory $documentFactory,
        private HtmlConverter $htmlConverter,
        private HtmlProcessingHub $htmlProcessingHub,
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('zoogle_document', [$this, 'zoogleDocument']),
        ];
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('zoogle_html', [$this, 'zoogleHtml'], ['is_safe' => ['html']]),
            new TwigFilter('zoogle_document_html', [$this, 'documentHtml'], ['is_safe' => ['html']]),
            new TwigFilter('zoogle_element_html', [$this, 'elementHtml'], ['is_safe' => ['html']]),
        ];
    }

    public function zoogleDocument(string $url): Document
    {
        return $this->documentFactory->fromUrl($url);
    }

    public function zoogleHtml(Document|DocumentElement $item): string
    {
        if ($item instanceof Document) {
            return $this->documentHtml($item);
        }

        return $this->elementHtml($item);
    }

    public function documentHtml(Document $document): string
    {
        $html = $this->htmlConverter->convert($document);

        return $this->htmlProcessingHub->process($html);
    }

    public function elementHtml(DocumentElement $element): string
    {
        return $this->htmlConverter->renderItem($element);
    }
}

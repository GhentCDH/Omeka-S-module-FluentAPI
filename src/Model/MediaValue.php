<?php

namespace FluentAPI\Model;

class MediaValue implements ValueInterface
{
    private string $source;
    private string $ingester;
    /**
     * @var FieldValue[]
     */
    private array $values;
    private bool $isPublic;

    public function __construct(string $source, string $ingester, array $values = [], bool $isPublic = true)
    {
        $this->source = $source;
        $this->ingester = $ingester;
        $this->values = $values;
        $this->isPublic = $isPublic;
    }

    public static function IIIFImage(string $url, string $label, string $thumbnailUrl = null): static
    {
        return new static(
            $url,
            'iiif',
            [
                new Literal('dcterms:title', 'Title', $label),
                'thumbnail-url' => $thumbnailUrl,
            ]
        );
    }

    public static function IIIFImageThumbnail(string $url, string $label, string $thumbnailService): static
    {
        return new static(
            $url,
            'iiif',
            [
                new Literal('dcterms:title', 'Title', $label),
                'thumbnail-service' => $thumbnailService,
                'thumbnail-size' => 512,
            ]
        );
    }

    public function addField(FieldValue $fieldValue): void
    {
        $this->values[$fieldValue->getTerm()] = $fieldValue;
    }

    public function getFields(): array
    {
        return $this->values;
    }

    public function export(): array
    {
        $mediaItem = [];

        $mediaItem['o:is_public'] = $this->isPublic;
        $mediaItem['o:source'] = $this->source;
        $mediaItem['o:ingester'] = $this->ingester;

        foreach ($this->values as $key => $value) {
            if ($value instanceof FieldValue) {
                $mediaItem[$value->getTerm()] = $value->export();
            } else {
                $mediaItem[$key] = $value;
            }
        }

        return $mediaItem;
    }
}

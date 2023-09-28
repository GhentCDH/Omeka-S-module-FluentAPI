<?php
namespace FluentAPI\Repository;

use Omeka\Api\Representation\ItemRepresentation;

/**
 * @method ItemRepresentation[] search(array $searchParameters = [], array $requestOptions = [])
 * @method ItemRepresentation read(int $id)
 */
class MediaRepository extends AbstractRdfRepository implements MediaRepositoryInterface
{

    public final static function getResourceType(): string
    {
        return 'media';
    }

    public function itemId(int $id): static
    {
        return $this->setSearchParameter('item_id', $id);
    }

    public function mediaType(string $type): static
    {
        return $this->setSearchParameter('media_type', $type);
    }

    public function siteId(int $id): static
    {
        return $this->setSearchParameter('site_id', $id);
    }

    public function ingester(string $ingester): static
    {
        return $this->setSearchParameter('ingester', $ingester);
    }

    public function renderer(string $renderer): static
    {
        return $this->setSearchParameter('renderer', $renderer);
    }
}

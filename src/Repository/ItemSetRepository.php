<?php
namespace FluentAPI\Repository;

use Omeka\Api\Representation\ItemSetRepresentation;

/**
 * @method ItemSetRepresentation[] search(array $searchParameters = [], array $requestOptions = [])
 * @method ItemSetRepresentation read(int $id)
 */
class ItemSetRepository extends AbstractRdfRepository implements ItemSetRepositoryInterface
{
    public final static function getResourceType(): string
    {
        return 'item_sets';
    }

    public function isOpen(bool $state): static
    {
        return $this->setSearchParameter('is_open', $state);
    }

    public function siteId(int $id): static
    {
        return $this->setSearchParameter('site_id', $id);
    }

}

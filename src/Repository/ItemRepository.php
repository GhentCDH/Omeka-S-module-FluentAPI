<?php
namespace FluentAPI\Repository;

use Omeka\Api\Representation\ItemRepresentation;

/**
 * @method ItemRepresentation[] search(array $searchParameters = [], array $requestOptions = [])
 * @method ItemRepresentation read(int $id)
 */
class ItemRepository extends AbstractRdfRepository implements ItemRepositoryInterface
{

    public final static function getResourceType(): string
    {
        return 'items';
    }

    public function siteId(int $id): static
    {
        return $this->setSearchParameter('site_id', $id);
    }

    public function itemSetId(int $id): static
    {
        return $this->setSearchParameter('item_set_id', $id);
    }

    public function siteAttachmentsOnly(bool $state): static
    {
        return $this->setSearchParameter('site_attachments_only', $state);
    }

}

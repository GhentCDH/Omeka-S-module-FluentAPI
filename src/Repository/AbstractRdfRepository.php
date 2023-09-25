<?php
namespace FluentAPI\Repository;

use FluentAPI\Model\ItemRequest;
use Omeka\Api\Response;

abstract class AbstractRdfRepository extends AbstractRepository implements RdfRepositoryInterface
{
    protected static function getResourceTemplate(): ?string
    {
        return null;
    }

    /* overrides */

    public function create(ItemRequest $itemRequest, array $fileData = [], array $requestOptions = []): Response
    {
        if ( static::getResourceTemplate() ) {
            $this->saturator->addResourceTemplateByName(static::getResourceTemplate(), $itemRequest);
        }
        return parent::create($itemRequest, $fileData, $requestOptions);
    }

    protected function getDefaultSearchParameters(): array
    {
        $ret = [];
        if ( static::getResourceTemplate() ) {
            $ret['resource_template_id'] = (int) $this->saturator->getResourceTemplateByName(static::getResourceTemplate())->id();
        }
        return $ret;
    }

    /* Common parameters */
    public function any(?string $value): static
    {
        return $this->setSearchParameter('search', $value);
    }

    public function fullText(?string $value): static
    {
        return $this->setSearchParameter('fulltext_search', $value);
    }

    /* Parameters for RDF resources */

    public function ownerId(?int $id): static
    {
        return $this->setSearchParameter('owner_id', $id);
    }

    public function resourceClassLabel(?string $label): static
    {
        return $this->setSearchParameter('resource_class_label', $label);
    }

    public function resourceClassId(?int $id): static
    {
        return $this->setSearchParameter('resource_class_id', $id);
    }

    public function resourceTemplateId(?int $id): static
    {
        return $this->setSearchParameter('resource_template_id', $id);
    }

    public function isPublic(?bool $state): static
    {
        return $this->setSearchParameter('is_public', $state);
    }

    public function property(string $property, string $value, string $searchType = 'eq', string $joiner = 'and') : static
    {
        $this->searchParameters['property'] = $this->searchParameters['property'] ?? [];
        $this->searchParameters['property'][] = [
            'property' => $property,
            'type' => $searchType,
            'text' => $value,
            'joiner' => $joiner
        ];
        return $this;
    }

    /*
     * Shortcuts
     */

    public function getByProperty(string $property, string $value, string $searchType = 'eq'): array
    {
        $query = [
            'property' => [
                [
                    'property' => $this->saturator->loadPropertyId($property),
                    'type' => $searchType,
                    'text' => $value
                ]
            ]
        ];

        return $this->search($query);
    }

}

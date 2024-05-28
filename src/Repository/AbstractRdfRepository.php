<?php
namespace FluentAPI\Repository;

use FluentAPI\Model\ItemRequest;
use Omeka\Api\Response;

abstract class AbstractRdfRepository extends AbstractRepository implements RdfRepositoryInterface
{
    public const SEARCH_TYPE_EQUALS = 'eq';
    public const SEARCH_TYPE_CONTAINS = 'in';
    public const SEARCH_TYPE_STARTS_WITH = 'sw';
    public const SEARCH_TYPE_ENDS_WITH = 'ew';
    public const SEARCH_TYPE_RESOURCE_ID = 'res';
    public const SEARCH_TYPE_ANY_VALUE = 'ex';
    public const JOIN_AND = 'and';
    public const JOIN_OR = 'or';


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

    public function resourceClassId(int|array|null $id): static
    {
        return $this->setSearchParameter('resource_class_id', $id);
    }

    public function resourceTemplateId(int|array|null $id): static
    {
        return $this->setSearchParameter('resource_template_id', $id);
    }

    public function resourceTemplateLabel(string|array|null $label): static
    {
        if (is_array($label)) {
            $ids = array_map(fn($v) => $this->saturator->getResourceTemplateByName($v)->id(), $label);
            return $this->setSearchParameter('resource_template_id', $ids);
        } else {
            return $this->setSearchParameter('resource_template_label', $label);
        }
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

    public function modifiedBefore(string $date): static
    {
        return $this->setSearchParameter('modified_before', $date);
    }

    public function modifiedAfter(string $date): static
    {
        return $this->setSearchParameter('modified_after', $date);
    }

    public function createdBefore(string $date): static
    {
        return $this->setSearchParameter('created_before', $date);
    }

    public function createdAfter(string $date): static
    {
        return $this->setSearchParameter('created_after', $date);
    }


}

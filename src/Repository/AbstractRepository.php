<?php
namespace FluentAPI\Repository;

use FluentAPI\Model\ItemRequest;
use FluentAPI\Utility\PropertyIdSaturator;
use Omeka\Api\Manager;
use Omeka\Api\Response;

abstract class AbstractRepository implements RepositoryInterface
{
    protected Manager $api;
    protected PropertyIdSaturator $saturator;
    protected array $searchParameters = [];
    protected array $requestOptions = [];

    protected bool $noDefaults = false;

    // sort options
    public const SORT_ASC = 'asc';
    public const SORT_DESC = 'desc';

    // responseContent options
    public const RESPONSE_CONTENT_REPRESENTATION = 'representation';
    public const RESPONSE_CONTENT_REFERENCE = 'reference';
    public const RESPONSE_CONTENT_RESOURCE = 'resource';

    // collectionOptions
    public const COLLECTION_ACTION_REPLACE = 'replace';
    public const COLLECTION_ACTION_APPEND = 'append';
    public const COLLECTION_ACTION_REMOVE = 'remove';


    public function __construct(Manager $api, PropertyIdSaturator $saturator)
    {
        $this->api = $api;
        $this->saturator = $saturator;
    }

    public function qb(): static
    {
        return (clone $this)->reset();
    }

    /**
     * @return Manager
     */
    public function api(): Manager {
        return $this->api;
    }

    public function noDefaults(): static {
        $this->noDefaults = true;
        return $this;
    }

    public function create(ItemRequest $itemRequest, array $fileData = [], array $requestOptions = []): Response
    {
        // Saturate.
        $this->saturator->addPropertyIds($itemRequest);

        // Export
        $toInsert = $itemRequest->export();

        // create
        return $this->api->create(
            static::getResourceType(),
            $toInsert,
            $fileData,
            $this->getRequestOptions($requestOptions)
        );
    }

    public function delete(string $id): Response
    {
        // delete
        return $this->api->delete(static::getResourceType(), $id);
    }

    public function deleteAll(): Response
    {
        $ids = [];
        $items = $this->search();
        foreach($items as $item) {
            $ids[] = $item->id();
        }
        return $this->api->batchDelete(static::getResourceType(), $ids, [], ['continueOnError' => true]);
    }

    public function update(string $id, ItemRequest $itemRequest, array $fileData = [], array $requestOptions = []): Response
    {
        // Saturate
        $this->saturator->addPropertyIds($itemRequest);

        // Export
        $toUpdate = $itemRequest->export();

        // Update
        return $this->api->update(
            static::getResourceType(),
            $id,
            $toUpdate,
            $fileData,
            $this->getRequestOptions($requestOptions)
        );
    }

    public function search(array $searchParameters = [], array $requestOptions = []): array {
        return $this->api->search(
            static::getResourceType(),
            $this->getSearchParameters($searchParameters),
            $this->getRequestOptions($requestOptions)
        )->getContent();
    }

    public function read(int $id)
    {
        return $this->api->read(static::getResourceType(), $id, $this->getSearchParameters(), $this->getRequestOptions())->getContent();
    }

    public function reset(): static
    {
        $this->resetSearchParameters();
        $this->resetRequestOptions();
        $this->noDefaults = false;
        return $this;
    }

    protected function getDefaultSearchParameters(): array
    {
        return [];
    }

    public function getSearchParameters(array $additional_options = []): array
    {
        if ( $this->noDefaults ) {
            return array_merge($this->searchParameters, $additional_options);
        } else {
            return array_merge($this->getDefaultSearchParameters(), $this->searchParameters, $additional_options);
        }
    }

    public function resetSearchParameters(): static {
        $this->searchParameters = [];
        return $this;
    }

    public function setSearchParameter(string $parameter, $value): static
    {
        if ( $value === null ) {
            unset($this->searchParameters[$parameter]);
        }
        $this->searchParameters[$parameter] = $value;
        return $this;
    }

    public function getSearchParameter(string $parameter): mixed
    {
        return $this->searchParameters[$parameter] ?? null;
    }

    public function id(int|array|string $value): static
    {
        return $this->setSearchParameter('id', $value);
    }

    public function sortBy(string $field): static
    {
        return $this->setSearchParameter('sortBy', $field);
    }

    public function sortOrder(string $order): static
    {
        return $this->setSearchParameter('sortOrder', $order);
    }

    public function sort(string $field, string $mode = self::SORT_DESC): static
    {
        $this->sortBy($field);
        return $this->sortOrder($mode);
    }

    public function sortAsc(): static {
        return $this->setSearchParameter('sort_order', self::SORT_ASC);
    }

    public function sortDesc(): static {
        return $this->setSearchParameter('sort_order', self::SORT_DESC);
    }

    public function limit(int $limit = 0): static
    {
        if ($limit > 0) {
            $this->searchParameters['limit'] = $limit;
        } else {
            unset($this->searchParameters['limit']);
        }
        return $this;
    }

    public function offset(int $offset = 0): static
    {
        return $this->setSearchParameter('offset', $offset);
    }

    public function perPage(int $results): static
    {
        return $this->setSearchParameter('per_page', $results);
    }

    public function page(int $page): static
    {
        return $this->setSearchParameter('page', $page);
    }

    /*
     *  Request options
     */

    public function resetRequestOptions(): static {
        $this->requestOptions = [];
        return $this;
    }

    public function getRequestOptions(array $additional_options = []): array
    {
        return array_merge($this->requestOptions, $additional_options);
    }

    public function setRequestOption(string $option, $value): static
    {
        $this->requestOptions[$option] = $value;
        return $this;
    }

    public function getRequestOption(string $option): mixed
    {
        return $this->requestOptions[$option] ?? null;
    }

    public function responseContent(string $content_type): static
    {
        if ( in_array($content_type, [
            self::RESPONSE_CONTENT_REFERENCE,
            self::RESPONSE_CONTENT_REPRESENTATION,
            self::RESPONSE_CONTENT_RESOURCE
        ], TRUE) ) {
            return $this->setRequestOption('responseContent', $content_type);
        }
        return $this;
    }

    public function collectionAction(string $action): static
    {
        if ( in_array($action, [
            self::COLLECTION_ACTION_REMOVE,
            self::COLLECTION_ACTION_REPLACE,
            self::COLLECTION_ACTION_APPEND
        ], TRUE) ) {
            return $this->setRequestOption('collectionAction', $action);
        }
        return $this;
    }

    public function initialize(bool $state = true) : static
    {
        return $this->setRequestOption('initialize', $state);
    }

    public function finalize(bool $state = true) : static
    {
        return $this->setRequestOption('finalize', $state);
    }

    public function isPartialUpdate(bool $state = true) : static
    {
        return $this->setRequestOption('isPartial', $state);
    }

    public function continueOnError(bool $state = false) : static
    {
        return $this->setRequestOption('continueOnError', $state);
    }

    public function flushEntityManager(bool $state = true) : static
    {
        return $this->setRequestOption('flushEntityManager', $state);
    }

    public function returnScalar(string|bool $field): static
    {
        return $this->setRequestOption('returnScalar', $field);
    }

    public function respondRepresentation() : static
    {
        return $this->setRequestOption('responseContent', 'representation');
    }

    public function respondResource() : static
    {
        return $this->setRequestOption('responseContent', 'resource');
    }

    public function respondReference() : static
    {
        return $this->setRequestOption('responseContent', 'reference');
    }

}

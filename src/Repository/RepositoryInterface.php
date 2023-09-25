<?php
namespace FluentAPI\Repository;

use FluentAPI\Model\ItemRequest;
use Omeka\Api\Manager;

interface RepositoryInterface
{
    public function api(): Manager;

    public function qb(): static;

    public function update(string $id, ItemRequest $itemRequest, array $fileData = [], array $requestOptions = []);
    public function create(ItemRequest $itemRequest, array $fileData = [], array $requestOptions = []);
    public function search(array $searchParameters = [], array $requestOptions = []);
    public function read(int $id);

    public function reset(): static;

    /*
     * Search parameters
     */

    public function getSearchParameters(array $additional_options = []): array;
    public function getSearchParameter(string $parameter): mixed;
    public function setSearchParameter(string $parameter, $value): static;
    public function resetSearchParameters(): static;

    public function id(int|array $value): static;
    public function page(int $page): static;
    public function perPage(int $results): static;
    public function limit(int $limit = 0): static;
    public function offset(int $offset = 0): static;
    public function sortBy(string $field): static;
    public function sortOrder(string $order): static;

    /*
     *  Request options
     */

    public function getRequestOptions(array $additional_options = []): array;
    public function getRequestOption(string $option): mixed;
    public function setRequestOption(string $option, $value): static;
    public function resetRequestOptions(): static;

    public function initialize(bool $state = true) : static;
    public function finalize(bool $state = true) : static;
    public function isPartialUpdate(bool $state = false) : static;
    public function continueOnError(bool $state = false) : static;
    public function flushEntityManager(bool $state = true) : static;

    public function returnScalar(string|bool $field): static;
    public function collectionAction(string $action): static;
    public function responseContent(string $content_type): static;
}

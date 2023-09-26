<?php
namespace FluentAPI\Repository;

interface ItemRepositoryInterface extends RdfRepositoryInterface
{
    public function siteAttachmentsOnly(bool $state): static;
    public function siteId(int $id): static;
    public function itemSetId(int|array $id): static;
    public function notItemSetId(int|array $id): static;
    public function hasMedia(): static;
}

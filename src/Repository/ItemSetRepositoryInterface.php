<?php
namespace FluentAPI\Repository;

interface ItemSetRepositoryInterface extends RdfRepositoryInterface
{
    public function isOpen(bool $state): static;
    public function siteId(int $id): static;
}


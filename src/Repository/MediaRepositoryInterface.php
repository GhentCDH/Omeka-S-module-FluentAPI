<?php
namespace FluentAPI\Repository;

interface MediaRepositoryInterface
{
    public function itemId(int $id): static;
    public function mediaType(bool $type): static;
    public function siteId(int $id): static;
    public function ingester(string $ingester): static;
    public function renderer(string $renderer): static;
}